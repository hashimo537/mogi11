<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    // ログアウトができる
    public function test_ログアウトができる()
    {
        // ユーザーを作成してログイン状態にする
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        $this->actingAs($user);

        // ログアウトを実行
        $response = $this->post('/logout');

        // ログアウトされているか確認
        $this->assertGuest();
    }
}