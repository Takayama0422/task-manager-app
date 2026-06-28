<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressLog extends Model
{
    use HasFactory;

    // 🌟【重要】一括保存（作成・更新）を許可するカラムをここにすべて指定します
    protected $fillable = [
        'user_id',
        'textbook_id',
        'status',
        'is_flagged',
        'memo',
    ];

    /**
     * 必要であれば、Textbookモデルへのリレーション定義もここにあります
     */
    public function textbook()
    {
        return $this->belongsTo(Textbook::class);
    }
}
