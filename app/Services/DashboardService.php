<?php

namespace App\Services;

use App\Models\Textbook;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private TextbookCategoryService $categoryService
    ) {}

    public function getDashboardData(int $userId): array
    {
        $allTextbooks = Textbook::where('user_id', $userId)
            ->with(['progressLog' => fn ($query) => $query->where('user_id', $userId)])
            ->get();

        return [
            'textbooks' => $this->groupTextbooks($allTextbooks),
            'categories' => $this->categoryService->getCategories(),
            'progressRate' => $this->calcProgressRate($allTextbooks),
            'attentionChapters' => $this->extractAttentionChapters($allTextbooks),
        ];
    }

    private function calcProgressRate(Collection $textbooks): int
    {
        return $this->calcCompletionRate($textbooks);
    }

    private function groupTextbooks(Collection $textbooks): Collection
    {
        return $textbooks->groupBy('major_id')->map(function (Collection $group) {
            return [
                'items' => $group,
                'progress_rate' => $this->calcCompletionRate($group),
            ];
        });
    }

    /**
     * フラグあり または メモありのチャプターを抽出
     */
    private function extractAttentionChapters(Collection $textbooks): Collection
    {
        return $textbooks->filter(function (Textbook $textbook) {
            $log = $textbook->progressLog;

            return $log->is_flagged || ! empty($log->memo);
        });
    }

    private function calcCompletionRate(Collection $textbooks): int
    {
        $total = $textbooks->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $textbooks->where('is_completed', true)->count();

        return (int) round(($completed / $total) * 100);
    }
}
