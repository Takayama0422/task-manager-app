<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Textbook extends Model
{
    use HasFactory;

    // 正方向のリレーション：教材マスタから見て、進捗ログは1つ存在する
   public function progressLog()
    {
        // 🌟 hasOne に where 条件をチェーンし、現在ログインしているユーザーのログだけを対象にします
        return $this->hasOne(ProgressLog::class)->where('user_id', auth()->id());
    }
}