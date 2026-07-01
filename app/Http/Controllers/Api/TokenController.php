<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssueTokenRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenController extends Controller
{
    /**
     * メールアドレスとパスワードで認証し、APIトークンを発行する
     *
     * リクエスト例:
     * POST /api/token
     * { "email": "user@example.com", "password": "password" }
     *
     * レスポンス例:
     * { "token": "1|abc123xyz..." }
     */
    public function issue(IssueTokenRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        $user->tokens()->where('name', 'api-access')->delete();

        $token = $user->createToken('api-access')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    /**
     * 現在のトークンを失効させる（ログアウト相当）
     *
     * リクエスト例:
     * DELETE /api/token
     * Authorization: Bearer 1|abc123xyz...
     */
    public function revoke(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if (! $token) {
            return response()->json([
                'message' => '有効なトークンが見つかりませんでした。',
            ], 401);
        }

        $token->delete();

        return response()->json(['message' => 'トークンを失効させました。']);
    }
}
