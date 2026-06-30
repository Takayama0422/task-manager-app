<?php

namespace App\Services;

use App\Models\Textbook;

class DashboardService
{
    /**
     * ダッシュボード表示に必要なデータを全て生成して返す
     */
    public function getDashboardData(int $userId): array
    {
        $allTextbooks = Textbook::where('user_id', $userId)
            ->with(['progressLog' => fn ($query) => $query->where('user_id', $userId)])
            ->get();

        return [
            'textbooks' => $this->groupTextbooks($allTextbooks),
            'categories' => config('textbooks.categories'),
            'progressRate' => $this->calcProgressRate($allTextbooks),
            'flaggedChapters' => $this->extractFlaggedChapters($allTextbooks),
        ];
    }

    /**
     * 全体進捗率（%）を計算
     */
    private function calcProgressRate($textbooks): int
    {
        $total = $textbooks->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $textbooks->where('is_completed', true)->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * major_id ごとにグループ化し、カード単位の進捗率を付与
     */
    private function groupTextbooks($textbooks)
    {
        return $textbooks->groupBy('major_id')->map(function ($group) {
            $total = $group->count();
            $completed = $group->where('is_completed', true)->count();

            $group->progress_rate = $total > 0
                ? (int) round(($completed / $total) * 100)
                : 0;

            return $group;
        });
    }

    /**
     * フラグあり または メモありのチャプターを抽出
     */
    private function extractFlaggedChapters($textbooks)
    {
        return $textbooks->filter(function ($textbook) {
            $log = $textbook->progressLog;

            return $log->is_flagged || ! empty($log->memo);
        });
    }
}