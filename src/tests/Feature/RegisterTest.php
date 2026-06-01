<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // ① 名前が未入力の場合
    public function test_名前が未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ]);
        $response->assertSessionHasErrors(['name' => 'ユーザー名を入力してください']);
    }

    // ② メールアドレスが未入力の場合
    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    // ③ パスワードが未入力の場合
    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    // ④ パスワードが7文字以下の場合
    public function test_パスワードが7文字以下の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'test123',
            'password_confirmation' => 'test123',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    // ⑤ パスワードと確認用が一致しない場合
    public function test_パスワードと確認用パスワードが一致しない場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'test1234',
            'password_confirmation' => 'test5678',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }


    // ⑥ 全項目正常入力で登録成功
    public function test_全項目正常入力で会員登録できる()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ]);

        // DBに登録されているか
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        // メール認証画面にリダイレクトされるか
        $response->assertRedirect('/email/verify');
    }

}