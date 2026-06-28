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
        Schema::create('progress_logs', function (Blueprint $table) {
            $table->id();

            // 外部キー：誰の、どの教材項目に対するログか
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('textbook_id')->constrained()->onDelete('cascade');

            // 進捗ステータス（未着手:0 / 学習中:1 / 完了:2 などの数値で管理すると軽量かつスマートです）
            $table->tinyInteger('status')->default(0);

            // 面談・質問用フラグ（0: 通常 / 1: メンターに質問したい項目）
            $table->tinyInteger('is_flagged')->default(0);

            // 学習ログ・使えるコード単語帳（テキスト入力用）
            $table->text('memo')->nullable();

            $table->timestamps();

            // 1人のユーザーが、1つの教材項目に対して持てるログは「1つだけ」にする一意制約
            $table->unique(['user_id', 'textbook_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_logs');
    }
};
