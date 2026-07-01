<?php

namespace Tests\Feature;

use App\Models\ProgressLog;
use App\Models\Textbook;
use App\Models\User;
use Tests\TestCase;

class TextbookStatusUpdateTest extends TestCase
{
    /** @test */
    public function 自分の教材の進捗を更新できる(): void
    {
        $user = User::factory()->create();
        $textbook = Textbook::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(
            route('textbooks.updateStatus', $textbook->id),
            [
                'status' => ProgressLog::STATUS_IN_PROGRESS,
                'is_flagged' => true,
                'memo' => 'テストメモ',
            ]
        );

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('progress_logs', [
            'user_id' => $user->id,
            'textbook_id' => $textbook->id,
            'status' => ProgressLog::STATUS_IN_PROGRESS,
            'is_flagged' => true,
            'memo' => 'テストメモ',
        ]);
    }

    /** @test */
    public function 他人の教材の進捗は更新できない(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $textbook = Textbook::factory()->create(['user_id' => $userB->id]);

        $this->actingAs($userA)->postJson(
            route('textbooks.updateStatus', $textbook->id),
            [
                'status' => ProgressLog::STATUS_COMPLETED,
                'is_flagged' => false,
                'memo' => null,
            ]
        )->assertForbidden();

        $this->assertDatabaseMissing('progress_logs', [
            'user_id' => $userA->id,
            'textbook_id' => $textbook->id,
        ]);
    }
}
