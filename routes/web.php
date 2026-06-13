<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TextbookController;

// 🟢 誰でもアクセスできるルート（例：ウェルカム画面など、必要に応じて）
Route::get('/', function () {
    return redirect()->route('login');
});

// 🌟【修正】ログイン（認証）しているユーザーだけがアクセスできるグループ
Route::middleware(['auth'])->group(function () {
    
    // 教材一覧画面
    Route::get('/textbooks', [TextbookController::class, 'index'])->name('textbooks.index');
    
    // 教材詳細画面
    Route::get('/textbooks/{major_id}', [TextbookController::class, 'show'])->name('textbooks.show');
    
    // 非同期進捗更新API
    Route::post('/textbooks/{id}/status', [TextbookController::class, 'updateStatus'])->name('textbooks.updateStatus');

    // 🌟【新規追加】教材名編集（表示と更新）
    Route::get('/textbooks/{major_id}/edit', [TextbookController::class, 'edit'])->name('textbooks.edit');
    Route::put('/textbooks/{major_id}', [TextbookController::class, 'updateName'])->name('textbooks.updateName');

});