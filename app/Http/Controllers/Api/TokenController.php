<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function issue(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        // ユーザーが存在しない、またはパスワードが一致しない場合
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        // トークンを発行して返す
        // 'api-access' はトークン名（用途の識別子）
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
        // 現在のリクエストに使われたトークンのみを削除
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'トークンを失効させました。']);
    }
}