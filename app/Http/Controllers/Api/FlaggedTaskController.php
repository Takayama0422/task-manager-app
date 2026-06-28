<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgressLog;
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

        $flaggedLogs = ProgressLog::with('textbook')
            ->join('textbooks', 'progress_logs.textbook_id', '=', 'textbooks.id')
            ->where('progress_logs.user_id', $userId)
            ->where('progress_logs.is_flagged', 1)
            ->orderBy('textbooks.major_id')
            ->orderBy('textbooks.mid_sort')
            ->orderBy('textbooks.chapter_no')
            ->select('progress_logs.*')
            ->get();

        $tasks = $flaggedLogs->map(fn(ProgressLog $log) => [
            'id'         => $log->textbook->id,
            'major_id'   => $log->textbook->major_id,
            'mid_sort'   => $log->textbook->mid_sort,
            'chapter_no' => $log->textbook->chapter_no,
            'status'     => $log->status,
            'memo'       => $log->memo,
            'flagged_at' => $log->updated_at->toIso8601String(),
        ]);

        return response()->json([
            'user_id' => $userId,
            'count'   => $tasks->count(),
            'tasks'   => $tasks->values(),
        ]);
    }
}