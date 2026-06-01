<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ① メールアドレスが未入力の場合
    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'test1234',
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    // ② パスワードが未入力の場合
    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    // ③ 登録されていない情報でログインした場合
    public function test_登録されていない情報の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'test1234',
        ]);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    // ④ 正しい情報でログインできる
    public function test_正しい情報が入力された場合ログイン処理が実行される()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',  // ← これが抜けている
            'email' => 'test@example.com',
            'password' => bcrypt('test1234'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'test1234',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}