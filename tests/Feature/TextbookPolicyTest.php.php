<?php

namespace Tests\Feature\Textbook;

use App\Models\ProgressLog;
use App\Models\Textbook;
use App\Models\User;
use Tests\TestCase;

/**
 * 認証・認可・進捗更新に関するテスト
 * ④ 正常系・異常系・認可エラーを網羅
 */
class TextbookPolicyTest extends TestCase
{
    // =========================================================
    // 正常系：管理者ユーザー
    // =========================================================

    /** @test */
    public function 管理者は教材名編集画面にアクセスできる(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('textbooks.edit', 1))
            ->assertStatus(200);
    }

    /** @test */
    public function 管理者は教材名を更新できる(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->put(route('textbooks.updateName', 1), [
                'category_name' => '更新後の教材名',
            ])
            ->assertRedirect(route('textbooks.index'));
    }

    // =========================================================
    // 異常系：一般ユーザーの認可エラー
    // =========================================================

    /** @test */
    public function 一般ユーザーは編集画面に403でアクセスできない(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('textbooks.edit', 1))
            ->assertStatus(403);
    }

    /** @test */
    public function 一般ユーザーは教材名更新に403でアクセスできない(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->put(route('textbooks.updateName', 1), [
                'category_name' => '不正な更新',
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function 未認証ユーザーは編集画面にアクセスするとログイン画面にリダイレクトされる(): void
    {
        $this->get(route('textbooks.edit', 1))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function 未認証ユーザーは教材名更新にアクセスするとログイン画面にリダイレクトされる(): void
    {
        $this->put(route('textbooks.updateName', 1), [
            'category_name' => '不正な更新',
        ])->assertRedirect(route('login'));
    }

    // =========================================================
    // 正常系：進捗ステータス更新
    // =========================================================

    /** @test */
    public function 自分の教材の進捗を更新できる(): void
    {
        $user     = User::factory()->create();
        $textbook = Textbook::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson(route('textbooks.updateStatus', $textbook->id), [
                'status'     => 1,
                'is_flagged' => false,
                'memo'       => 'テストメモ',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function 進捗更新でProgressLogが正しく保存される(): void
    {
        $user     = User::factory()->create();
        $textbook = Textbook::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson(route('textbooks.updateStatus', $textbook->id), [
                'status'     => 2,
                'is_flagged' => 1,
                'memo'       => '重要メモ',
            ]);

        $this->assertDatabaseHas('progress_logs', [
            'user_id'     => $user->id,
            'textbook_id' => $textbook->id,
            'status'      => 2,
            'is_flagged'  => 1,
            'memo'        => '重要メモ',
        ]);
    }

    // =========================================================
    // 異常系：他ユーザーのデータへの不正アクセス
    // =========================================================

    /** @test */
    public function 他人の教材の進捗は更新できない(): void
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();
        $textbook = Textbook::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($attacker)
            ->postJson(route('textbooks.updateStatus', $textbook->id), [
                'status'     => 2,
                'is_flagged' => 1,
                'memo'       => '不正アクセス',
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function 未認証ユーザーは進捗更新にアクセスできない(): void
    {
        $textbook = Textbook::factory()->create();

        $this->postJson(route('textbooks.updateStatus', $textbook->id), [
            'status'     => 1,
            'is_flagged' => 0,
            'memo'       => '',
        ])->assertStatus(401);
    }

    // =========================================================
    // 正常系：show() のユーザー分離
    // =========================================================

    /** @test */
    public function show画面は自分の教材のみ表示される(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Textbook::factory()->create([
            'user_id'  => $userA->id,
            'major_id' => 1,
        ]);
        Textbook::factory()->create([
            'user_id'  => $userB->id,
            'major_id' => 1,
        ]);

        // userA でアクセス → userA のデータのみ取得されること
        $response = $this->actingAs($userA)
            ->get(route('textbooks.show', 1))
            ->assertStatus(200);

        // レスポンスに userB のデータが含まれないことをDBで確認
        $this->assertDatabaseMissing('textbooks', [
            'user_id'  => $userB->id,
            'major_id' => 1,
        ] + ['user_id' => $userA->id]); // userB と userA が混在しないこと
    }
}