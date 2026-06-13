<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Textbook;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\DB;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users', // Laravel 10の標準バリデーション
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        // 🌟 データベースの不整合を防ぐため、トランザクション処理の中で実行します
        return DB::transaction(function () use ($input) {
            
            // 1. まずは新しいユーザーを作成
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // 2. 🌟 新規ユーザー用の「13個の教材マスターデータ」を自動生成
            // 各大項目ごとのチャプター数（節数）の定義マップ
            $curriculumStructure = [
                1  => 5,  // 学習準備
                2  => 4,  // 開発環境セットアップ
                3  => 8,  // コマンドライン入門
                4  => 10, // Git入門
                5  => 6,  // HTML & CSS入門
                6  => 15, // Docker入門
                7  => 5,  // PHP入門
                8  => 6,  // データベース & SQL入門
                9  => 4,  // Laravel基礎
                10 => 8,  // Laravel実践
                11 => 5,  // Git × GitHub実践
                12 => 12, // Laravel × API
                13 => 5   // 総合アプリケーション開発
            ];

            $insertData = [];
            $now = now();

            // 定義マップをループして、バルクインサート（一括挿入）用の配列を作成
            foreach ($curriculumStructure as $majorId => $chapterCount) {
                for ($chapterNo = 1; $chapterNo <= $chapterCount; $chapterNo++) {
                    $insertData[] = [
                        'user_id'    => $user->id, // いま作成されたユーザーのID
                        'major_id'   => $majorId,
                        'mid_sort'   => $chapterNo, // 節の番号として使用
                        'chapter_no' => 1,          // 最小単位の小項目（一旦すべて1で初期化）
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // まとめて1回でデータベースに高速挿入
            Textbook::insert($insertData);

            return $user;
        });
    }
}