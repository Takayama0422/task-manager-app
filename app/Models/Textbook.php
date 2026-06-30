<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Textbook extends Model
{
    use HasFactory;

    // ④ config化に対応したアクセサ
    // ② display_title をモデルに移動
    public function getDisplayTitleAttribute(): string
    {
        return $this->major_id
            ? config('textbooks.categories')[$this->major_id] ?? '未定義のカテゴリ'
            : ($this->custom_title ?? '未定義のカテゴリ');
    }

    // ⑥ 完了判定アクセサ
    public function getIsCompletedAttribute(): bool
    {
        return $this->progressLog?->status == 2;
    }

    // ③ withDefault でコントローラーの if (!$progressLog) を不要に
    // auth()->id() への依存を廃止し、非認証コンテキスト（シーダー・テスト等）でも
    // 安全に動作するようにした。ユーザー絞り込みは呼び出し側（コントローラー）で行う。
    public function progressLog()
    {
        return $this->hasOne(ProgressLog::class)
            ->withDefault([
                'status' => 0,
                'is_flagged' => 0,
                'memo' => '',
            ]);
    }
}