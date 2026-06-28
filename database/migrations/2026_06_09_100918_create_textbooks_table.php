<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('textbooks', function (Blueprint $table) {
            $table->id();

            // 外部キー：どのユーザーのデータか（自分専用アプリだが、実務に倣ってUserと紐付け）
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // --- カリキュラム階層を数字で管理するカラム（NULL許容） ---
            $table->integer('major_id')->nullable();   // 大項目ID (1〜13)
            $table->integer('mid_sort')->nullable();   // 中項目の並び順
            $table->integer('chapter_no')->nullable(); // 小項目（チャプター）の連番

            // --- カスタム教材（技術書・Udemy等）用のカラム（NULL許容） ---
            $table->string('custom_title')->nullable(); // ユーザーが自由に入力した教材名

            $table->timestamps();

            // パフォーマンス向上のため、よく検索する数字の組み合わせにインデックスを貼る
            $table->index(['user_id', 'major_id', 'mid_sort', 'chapter_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textbooks');
    }
};
