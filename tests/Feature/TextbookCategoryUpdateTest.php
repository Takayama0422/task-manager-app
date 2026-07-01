<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TextbookCategoryService;
use Tests\TestCase;

class TextbookCategoryUpdateTest extends TestCase
{
    /** @test */
    public function 管理者は教材名を更新できる(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->put(
            route('textbooks.updateName', 1),
            ['category_name' => '更新後の教材名']
        );

        $response->assertRedirect(route('textbooks.index'));

        $this->assertDatabaseHas('category_overrides', [
            'major_id' => 1,
            'name' => '更新後の教材名',
        ]);

        $this->assertSame(
            '更新後の教材名',
            app(TextbookCategoryService::class)->getCategoryName(1)
        );
    }

    /** @test */
    public function 一般ユーザーは教材名を更新できない(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->put(
            route('textbooks.updateName', 1),
            ['category_name' => '不正な更新']
        )->assertForbidden();

        $this->assertDatabaseMissing('category_overrides', [
            'major_id' => 1,
            'name' => '不正な更新',
        ]);
    }

    /** @test */
    public function 存在しない大項目は404を返す(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put(
            route('textbooks.updateName', 99),
            ['category_name' => '存在しない']
        )->assertNotFound();
    }
}
