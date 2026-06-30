<?php

namespace Tests\Feature\Api;

use App\Models\ProgressLog;
use App\Models\Textbook;
use App\Models\User;
use Tests\TestCase;

class FlaggedTaskApiTest extends TestCase
{
    // =========================================================
    // 正常系
    // =========================================================

    /** @test */
    public function フラグ付き教材が正しい_jso_n構造で返る(): void
    {
        $user = User::factory()->create();
        $textbook = Textbook::factory()->create([
            'user_id' => $user->id,
            'major_id' => 1,
            'mid_sort' => 2,
            'chapter_no' => 3,
        ]);

        ProgressLog::factory()->create([
            'user_id' => $user->id,
            'textbook_id' => $textbook->id,
            'status' => 1,
            'is_flagged' => 1,
            'memo' => 'テストメモ',
        ]);

        // Sanctum認証：actingAsで認証済みユーザーとしてリクエスト
        $response = $this->actingAs($user)
            ->getJson('/api/flagged');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user_id',
                'count',
                'tasks' => [
                    '*' => [
                        'id',
                        'major_id',
                        'mid_sort',
                        'chapter_no',
                        'status',
                        'memo',
                        'flagged_at',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'user_id' => $user->id,
                'count' => 1,
            ]);
    }

    /** @test */
    public function フラグ付き教材が複数ある場合まとめて返る(): void
    {
        $user = User::factory()->create();

        foreach ([1, 2, 3] as $majorId) {
            $textbook = Textbook::factory()->create([
                'user_id' => $user->id,
                'major_id' => $majorId,
            ]);
            ProgressLog::factory()->create([
                'user_id' => $user->id,
                'textbook_id' => $textbook->id,
                'is_flagged' => 1,
            ]);
        }

        $response = $this->actingAs($user)
            ->getJson('/api/flagged');

        $response->assertStatus(200)
            ->assertJsonFragment(['count' => 3]);
    }

    /** @test */
    public function フラグなし教材は返らない(): void
    {
        $user = User::factory()->create();
        $textbook = Textbook::factory()->create(['user_id' => $user->id]);

        ProgressLog::factory()->create([
            'user_id' => $user->id,
            'textbook_id' => $textbook->id,
            'is_flagged' => 0,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/flagged');

        $response->assertStatus(200)
            ->assertJsonFragment(['count' => 0]);
    }

    /** @test */
    public function 他ユーザーのフラグ付き教材は返らない(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $textbook = Textbook::factory()->create(['user_id' => $userB->id]);

        ProgressLog::factory()->create([
            'user_id' => $userB->id,
            'textbook_id' => $textbook->id,
            'is_flagged' => 1,
        ]);

        // userA でアクセス → userB のデータは返らない
        $response = $this->actingAs($userA)
            ->getJson('/api/flagged');

        $response->assertStatus(200)
            ->assertJsonFragment(['count' => 0]);
    }

    /** @test */
    public function フラグ付き教材がない場合は空配列で返る(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/flagged');

        $response->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'count' => 0,
                'tasks' => [],
            ]);
    }

    /** @test */
    public function major_id昇順で返る(): void
    {
        $user = User::factory()->create();

        foreach ([3, 1, 2] as $majorId) {
            $textbook = Textbook::factory()->create([
                'user_id' => $user->id,
                'major_id' => $majorId,
            ]);
            ProgressLog::factory()->create([
                'user_id' => $user->id,
                'textbook_id' => $textbook->id,
                'is_flagged' => 1,
            ]);
        }

        $response = $this->actingAs($user)
            ->getJson('/api/flagged');

        $tasks = $response->json('tasks');

        $this->assertEquals([1, 2, 3], array_column($tasks, 'major_id'));
    }

    // =========================================================
    // 異常系
    // =========================================================

    /** @test */
    public function 未認証ユーザーは401が返る(): void
    {
        $this->getJson('/api/flagged')
            ->assertStatus(401);
    }
}
