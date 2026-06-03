<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ②ログイン機能
    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'test1234',
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_登録されていない情報の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'test1234',
        ]);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

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

    //１３ユーザー情報取得
    public function test_プロフィール情報が表示される()
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'image' => 'profiles/test.jpg',
        ]);

        $sellItem = Item::create([
            'user_id' => $user->id,
            'name' => '出品商品',
            'price' => 1000,
            'condition' => 1,
            'description' => '説明',
            'image' => 'test.jpg',
        ]);

        $seller = User::factory()->create();

        $buyItem = Item::create([
            'user_id' => $seller->id,
            'name' => '購入商品',
            'price' => 2000,
            'condition' => 1,
            'description' => '説明',
            'image' => 'test.jpg',
            'is_sold' => true,
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
            'payment_method' => 1,
        ]);

        $response = $this->actingAs($user)
            ->get('/mypage');

        $response->assertSee('テスト太郎');
        $response->assertSee('出品商品');
    }
    // 14 ユーザー情報変更
    public function test_プロフィール編集画面に初期値が表示される()
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
        ]);

        $response = $this->actingAs($user)
            ->get('/mypage/profile');

        $response->assertSee('テスト太郎');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区');
        $response->assertSee('テストビル');
    }

    // 1５ 出品商品情報登録
    public function test_商品出品情報が保存される()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $category = \App\Models\Category::create([
            'name' => 'ファッション',
        ]);

        $this->actingAs($user)
            ->post('/items/sell', [
                'categories' => [$category->id],
                'condition' => 1,
                'name' => '出品テスト商品',
                'brand' => 'テストブランド',
                'description' => 'テスト説明',
                'price' => 3000,
                'image' => UploadedFile::fake()->create('test.jpg', 100),
            ]);
            

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => '出品テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 3000,
            'condition' => 1,
        ]);

        $item = Item::where('name', '出品テスト商品')->first();

        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);
    }
}