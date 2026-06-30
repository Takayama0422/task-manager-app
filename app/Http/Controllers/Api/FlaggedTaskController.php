<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FlaggedTaskResource;
use App\Models\Textbook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlaggedTaskController extends Controller
{
    /**
     * ログインユーザーのフラグ付き教材一覧をJSON形式で返す
     *
     * ① user_id をリクエストパラメーターから受け取る設計を廃止
     * Sanctum 認証により auth()->user() からユーザーを特定することで、
     * 他ユーザーのデータへのアクセスを防ぐ
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $flaggedTextbooks = Textbook::where('textbooks.user_id', $userId)
            ->whereHas('progressLog', fn ($query) => $query
                ->where('user_id', $userId)
                ->where('is_flagged', true))
            ->with(['progressLog' => fn ($query) => $query->where('user_id', $userId)])
            ->orderBy('major_id')
            ->orderBy('mid_sort')
            ->orderBy('chapter_no')
            ->get();

        return response()->json([
            'user_id' => $userId,
            'count' => $flaggedTextbooks->count(),
            'tasks' => FlaggedTaskResource::collection($flaggedTextbooks),
        ]);
    }
}