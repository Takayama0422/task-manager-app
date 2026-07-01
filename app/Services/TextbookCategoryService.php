<?php

namespace App\Services;

use App\Models\CategoryOverride;

class TextbookCategoryService
{
    public function getCategories(): array
    {
        $categories = config('textbooks.categories', []);
        $overrides = CategoryOverride::pluck('name', 'major_id')->all();

        return array_replace($categories, $overrides);
    }

    public function getCategoryName(int $majorId): string
    {
        return $this->getCategories()[$majorId] ?? '未定義のカテゴリ';
    }

    public function updateCategoryName(int $majorId, string $name): void
    {
        CategoryOverride::updateOrCreate(
            ['major_id' => $majorId],
            ['name' => $name]
        );
    }
}
