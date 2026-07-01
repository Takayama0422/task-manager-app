<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FlaggedTaskResource;
use App\Models\Textbook;
use Illuminate\Http\JsonResponse;

class FlaggedTaskController extends Controller
{
    /**
     * ログインユーザーのフラグ付き教材一覧をJSON形式で返す
     */
    public function index(): JsonResponse
    {
        $userId = auth()->id();

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
