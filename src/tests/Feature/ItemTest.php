<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    // ① 全商品を取得できる
    public function test_全商品が表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }

    // ② 購入済み商品は「Sold」と表示される
    public function test_購入済み商品にSoldが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        Item::create([
            'user_id' => $user->id,
            'name' => '購入済み商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
            'is_sold' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    // ③ 自分が出品した商品は表示されない
    public function test_自分が出品した商品は表示されない()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        Item::create([
            'user_id' => $user->id,
            'name' => '自分の商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        // 同じユーザーでログインして商品一覧を見る
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
    }
    // ④ いいねした商品だけが表示される
    public function test_いいねした商品だけが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']); // 追加

        $likedItem = Item::create([
            'user_id' => $otherUser->id, // 2 → $otherUser->id に変更
            'name' => 'いいねした商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        Item::create([
            'user_id' => $otherUser->id, // 2 → $otherUser->id に変更
            'name' => 'いいねしていない商品',
            'price' => 2000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        \App\Models\Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }

    // ⑤ マイリストの購入済み商品にSoldが表示される
    public function test_マイリストの購入済み商品にSoldが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']); // 追加

        $soldItem = Item::create([
            'user_id' => $otherUser->id, // 2 → $otherUser->id に変更
            'name' => '購入済みいいね商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
            'is_sold' => true,
        ]);

        \App\Models\Like::create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertSee('Sold');
    }
    // ⑦ 商品名で部分一致検索ができる
    public function test_商品名で部分一致検索ができる()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品ABC',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        Item::create([
            'user_id' => $otherUser->id,
            'name' => '全然違う商品',
            'price' => 2000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->actingAs($user)->get('/?search=テスト');
        $response->assertSee('テスト商品ABC');
        $response->assertDontSee('全然違う商品');
    }

    // ⑧ 検索状態がマイリストでも保持されている
    public function test_検索状態がマイリストでも保持されている()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品ABC',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        \App\Models\Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // マイリストで検索キーワードを保持して遷移
        $response = $this->actingAs($user)->get('/?tab=mylist&search=テスト');
        $response->assertSee('テスト商品ABC');
    }
    // ⑨ 商品詳細に必要な情報が表示される
    public function test_商品詳細に必要な情報が表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明文',
            'price' => 1000,
            'condition' => 1,
            'image' => 'test.jpg',
        ]);

        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('テスト説明文');
        $response->assertSee('¥1,000');  // ← 修正
    }

    // ⑩ 複数選択されたカテゴリが表示される
    public function test_複数選択されたカテゴリが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明文',
            'price' => 1000,
            'condition' => 1,
            'image' => 'test.jpg',
        ]);

        $category1 = \App\Models\Category::create(['name' => 'ファッション']);
        $category2 = \App\Models\Category::create(['name' => 'スポーツ']);
        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get('/item/' . $item->id);  // ← 修正
        $response->assertSee('ファッション');
        $response->assertSee('スポーツ');
    }
    // ⑪ いいねすると登録される
    public function test_いいねすると商品が登録される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        $this->actingAs($user)->post('/items/' . $item->id . '/like');

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    // ⑫ いいねを解除できる
    public function test_再度いいねを押すといいねが解除される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        // 一度いいね
        $this->actingAs($user)->post('/items/' . $item->id . '/like');

        // もう一度押して解除
        $this->actingAs($user)->post('/items/' . $item->id . '/like');

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
    // ⑬ ログイン済みユーザーはコメントを送信できる
    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->actingAs($user)->post('/items/' . $item->id . '/comment', [
            'comment' => 'テストコメント',
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    // ⑭ 未ログインユーザーはコメントを送信できない
    public function test_未ログインユーザーはコメントを送信できない()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->post('/items/' . $item->id . '/comment', [
            'comment' => 'テストコメント',
        ]);

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    // ⑮ コメントが未入力の場合バリデーションメッセージが表示される
    public function test_コメントが未入力の場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->actingAs($user)->post('/items/' . $item->id . '/comment', [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment' => 'コメントを入力してください']);
    }

    // ⑯ コメントが255文字以上の場合バリデーションメッセージが表示される
    public function test_コメントが255文字以上の場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->actingAs($user)->post('/items/' . $item->id . '/comment', [
            'comment' => str_repeat('あ', 256),
        ]);

        $response->assertSessionHasErrors(['comment' => 'コメントは255文字以内で入力してください']);
    }
}