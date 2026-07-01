<?php

namespace App\Models;

use App\Services\TextbookCategoryService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Textbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'major_id',
        'mid_sort',
        'chapter_no',
        'custom_title',
    ];

    public function getDisplayTitleAttribute(): string
    {
        if ($this->major_id) {
            return app(TextbookCategoryService::class)->getCategoryName($this->major_id);
        }

        return $this->custom_title ?? '未定義のカテゴリ';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->progressLog?->status === ProgressLog::STATUS_COMPLETED;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progressLog(): HasOne
    {
        return $this->hasOne(ProgressLog::class)
            ->withDefault([
                'status' => ProgressLog::STATUS_NOT_STARTED,
                'is_flagged' => false,
                'memo' => '',
            ]);
    }
}
