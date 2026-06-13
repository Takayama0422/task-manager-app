<?php

namespace App\Policies;

use App\Models\User;

class TextbookPolicy
{
    /**
     * 管理者専用の機能（編集画面の表示や更新など）を許可するか判定する
     */
    public function admin(User $user): bool
    {
        // 先ほどUserモデルに casts を入れたため、型キャストを意識した安全な真偽値評価ができます
        return $user->is_admin === true;
    }
}