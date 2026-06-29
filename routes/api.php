<?php

use App\Http\Controllers\Api\FlaggedTaskController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| 【認証フロー】
| 1. POST /api/token   → トークン発行（メール・パスワードで認証）
| 2. GET  /api/flagged → トークンをヘッダーに付けてアクセス
| 3. DELETE /api/token → トークン失効（ログアウト）
|
*/

// ① トークン発行（認証不要）
Route::post('/token', [TokenController::class, 'issue']);

// ② 認証済みユーザーのみアクセス可能
Route::middleware('auth:sanctum')->group(function () {

    // ログインユーザー情報の取得
    Route::get('/user', fn (Request $request) => $request->user());

    // フラグ付き教材一覧
    // GET /api/flagged
    // Authorization: Bearer {token} ヘッダーが必要
    Route::get('/flagged', [FlaggedTaskController::class, 'index']);

    // トークン失効（ログアウト）
    Route::delete('/token', [TokenController::class, 'revoke']);
});
