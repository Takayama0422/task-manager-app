<?php

namespace App\Services;

use App\Models\Textbook;
use Illuminate\Support\Collection;

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
    private function calcProgressRate(Collection $textbooks): int
    {
        $total = $textbooks->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $textbooks->where('is_completed', true)->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * major_id ごとにグループ化し、カード単位の進捗率を付与する
     *
     * Collection への動的プロパティ付与（PHP 8.2 非推奨）を避けるため、
     * 各グループを ['items' => Collection, 'progress_rate' => int] の配列で表現する。
     * Bladeテンプレート側で $group['items'] / $group['progress_rate'] として参照する。
     */
    private function groupTextbooks(Collection $textbooks): Collection
    {
        return $textbooks->groupBy('major_id')->map(function (Collection $group) {
            $total = $group->count();
            $completed = $group->where('is_completed', true)->count();

            return [
                'items' => $group,
                'progress_rate' => $total > 0
                    ? (int) round(($completed / $total) * 100)
                    : 0,
            ];
        });
    }

    /**
     * フラグあり または メモありのチャプターを抽出
     */
    private function extractFlaggedChapters(Collection $textbooks): Collection
    {
        return $textbooks->filter(function (Textbook $textbook) {
            $log = $textbook->progressLog;

            return $log->is_flagged || ! empty($log->memo);
        });
    }
}
