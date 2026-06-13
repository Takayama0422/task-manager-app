<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FlaggedTaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $userId = (int) $validated['user_id'];

        // progressLog() リレーションは auth()->id() に依存するため
        // ProgressLog を起点に textbooks を JOIN して取得
        $flaggedLogs = ProgressLog::with('textbook')
            ->join('textbooks', 'progress_logs.textbook_id', '=', 'textbooks.id')
            ->where('progress_logs.user_id', $userId)
            ->where('progress_logs.is_flagged', 1)
            ->orderBy('textbooks.major_id')
            ->orderBy('textbooks.mid_sort')
            ->orderBy('textbooks.chapter_no')
            ->select('progress_logs.*')
            ->get();

        $tasks = $flaggedLogs->map(fn($log) => [
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