<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TextbookSeeder extends Seeder
{
    public function run()
    {
        // 1. 外部キー制約のチェックを一時的に無効化（安全装置をオフ）
        Schema::disableForeignKeyConstraints();

        // 2. 既存データを一度安全にリセット
        DB::table('textbooks')->truncate();

        // 3. 外部キー制約のチェックを有効化（安全装置をオンに戻す）
        Schema::enableForeignKeyConstraints();

        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 13個のチュートリアルタイトル定義
        $titles = [
            1 => '学習準備',
            2 => '開発環境セットアップ',
            3 => 'コマンドライン入門',
            4 => 'Git入門',
            5 => 'HTML & CSS入門',
            6 => 'Docker入門',
            7 => 'PHP入門',
            8 => 'データベース & SQL入門',
            9 => 'Laravel基礎',
            10 => 'Laravel実践',
            11 => 'Git × GitHub実践',
            12 => 'Laravel × API',
            13 => '総合アプリケーション開発',
        ];

        // ご提示いただいた [大項目 => [中項目 => 小項目の総数]] のマトリクス
        $matrix = [
            1 => [1 => 6],
            2 => [1 => 5],
            3 => [1 => 8],
            4 => [1 => 3, 2 => 3, 3 => 5],
            5 => [1 => 7, 2 => 6, 3 => 2, 4 => 2],
            6 => [1 => 2, 2 => 6],
            7 => [1 => 6, 2 => 6, 3 => 4, 4 => 3],
            8 => [1 => 6, 2 => 7, 3 => 5, 4 => 4],
            9 => [1 => 8, 2 => 5, 3 => 9, 4 => 9, 5 => 5, 6 => 5],
            10 => [1 => 6, 2 => 4, 3 => 5, 4 => 6, 5 => 7, 6 => 6],
            11 => [1 => 7, 2 => 8, 3 => 2, 4 => 1],
            12 => [1 => 1, 2 => 4, 3 => 5, 4 => 1],
            13 => [1 => 4, 2 => 5, 3 => 3, 4 => 2, 5 => 1, 6 => 3, 7 => 1, 8 => 3],
        ];

        $data = [];
        $now = now();

        foreach ($matrix as $major => $midItems) {
            foreach ($midItems as $mid => $maxChapter) {
                for ($chapter = 1; $chapter <= $maxChapter; $chapter++) {
                    $data[] = [
                        'user_id' => 1,
                        'major_id' => $major,
                        'mid_sort' => $mid,
                        'chapter_no' => $chapter,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // 高速バルクインサート
        DB::table('textbooks')->insert($data);
    }
}