<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 先ほど作成した TextbookSeeder をここで呼び出すように登録
        $this->call([
            TextbookSeeder::class,
        ]);
    }
}