<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Textbook extends Model
{
    use HasFactory;

    /**
     * 教材の表示タイトルを返すアクセサ
     * major_id があれば config のカテゴリ名、なければ custom_title を使用
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->major_id
            ? config('textbooks.categories')[$this->major_id] ?? '未定義のカテゴリ'
            : ($this->custom_title ?? '未定義のカテゴリ');
    }

    /**
     * 学習完了しているかどうかを返すアクセサ
     * status == 2 を「完了」として扱う
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->progressLog?->status == 2;
    }

    /**
     * 進捗ログとのリレーション
     *
     * ② auth()->id() をモデル内で呼ばない設計に変更
     * user_id の絞り込みは Controller / Service 側で行う
     * withDefault により progressLog が null になることはなく、
     * Controller 側での null チェックが不要になる
     */
    public function progressLog()
    {
        return $this->hasOne(ProgressLog::class)
            ->withDefault([
                'status'     => 0,
                'is_flagged' => 0,
                'memo'       => '',
            ]);
    }
}