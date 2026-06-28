<?php

namespace App\Services;

use App\Models\Textbook;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * ダッシュボード表示に必要なデータをまとめて返す
     *
     * Controller から auth()->id() を受け取り、
     * Model が認証状態に依存しない設計を維持する（② 対応）
     *
     * @param int $userId ログイン中のユーザーID
     * @return array{textbooks: Collection, categories: array, progressRate: int, flaggedChapters: Collection}
     */
    public function getDashboardData(int $userId): array
    {
        $allTextbooks = Textbook::where('user_id', $userId)
            ->with('progressLog')
            ->get();

        return [
            'textbooks'       => $this->groupTextbooks($allTextbooks),
            'categories'      => config('textbook.categories'),
            'progressRate'    => $this->calcProgressRate($allTextbooks),
            'flaggedChapters' => $this->extractFlaggedChapters($allTextbooks),
        ];
    }

    /**
     * 全体の進捗率（%）を計算して返す
     * 完了ステータス（status == 2）のチャプター数 / 全チャプター数
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
     * major_id ごとにグループ化し、カード単位の進捗率を付与して返す
     */
    private function groupTextbooks(Collection $textbooks): Collection
    {
        return $textbooks->groupBy('major_id')->map(function (Collection $group) {
            $total     = $group->count();
            $completed = $group->where('is_completed', true)->count();

            $group->progress_rate = $total > 0
                ? (int) round(($completed / $total) * 100)
                : 0;

            return $group;
        });
    }

    /**
     * フラグあり または メモありのチャプターを抽出して返す
     * ダッシュボードの「要確認セクション」に使用
     */
    private function extractFlaggedChapters(Collection $textbooks): Collection
    {
        return $textbooks->filter(function (Textbook $textbook) {
            $log = $textbook->progressLog;

            return $log->is_flagged || !empty($log->memo);
        });
    }
}