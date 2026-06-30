<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    // =========================================================
    // 正常系
    // =========================================================

    /** @test */
    public function 正しい認証情報でログインできる(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('textbooks.index'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function ログアウトできる(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('logout'));

        $this->assertGuest();
    }

    // =========================================================
    // 異常系
    // =========================================================

    /** @test */
    public function 誤ったパスワードでログインできない(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function 存在しないメールアドレスでログインできない(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'notexist@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function メールアドレスなしはバリデーションエラー(): void
    {
        $this->post(route('login'), [
            'password' => 'password123',
        ])->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワードなしはバリデーションエラー(): void
    {
        $this->post(route('login'), [
            'email' => 'test@example.com',
        ])->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function 未認証ユーザーはindexにアクセスできない(): void
    {
        $this->get(route('textbooks.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function 認証済みユーザーはindexにアクセスできる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('textbooks.index'))
            ->assertStatus(200);
    }
}
