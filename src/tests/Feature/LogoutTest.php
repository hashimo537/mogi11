<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    //③ログアウト機能
    public function test_ログアウトができる()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        // ログアウトされているか確認
        $this->assertGuest();
    }
}