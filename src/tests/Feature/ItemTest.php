<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    // ④商品一覧取得
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
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
    }
    // ⑤マイリスト一覧取得
    public function test_いいねした商品だけが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $likedItem = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'いいねした商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
        ]);

        Item::create([
            'user_id' => $otherUser->id,
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
    public function test_マイリストの購入済み商品にSoldが表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $soldItem = Item::create([
            'user_id' => $otherUser->id,
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

    public function test_未ログイン時はマイリストに何も表示されない()
    {
        $user = User::factory()->create();

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => '説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->get('/?tab=mylist');

        $response->assertDontSee('テスト商品');
    }
    // ⑥商品検索機能
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

        $response = $this->actingAs($user)->get('/?tab=mylist&search=テスト');
        $response->assertSee('テスト商品ABC');
    }

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
        $response->assertSee('¥1,000');
    }

    // ⑦ 商品詳細情報取得
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
    // ⑧ いいね機能
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
    // ⑨コメント送信機能
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
    //⑩商品購入機能
    public function test_購入が完了するとDBに保存される()
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

        $response = $this->actingAs($user)
            ->withSession([
                'shipping_address_' . $item->id => [
                    'postal_code' => '123-4567',
                    'address' => 'テスト住所',
                    'building' => null,
                ],
                'purchase_payment_method_' . $item->id => 1, // ← 追加
            ])
            ->get('/purchase/' . $item->id . '/success');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
    public function test_購入した商品はSoldと表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => '購入テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
            'is_sold' => true,
        ]);

        $response = $this->get('/');
        $response->assertSee('Sold');
    }
    public function test_購入した商品がマイページに表示される()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $otherUser = User::factory()->create(['name' => '他のユーザー']);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => '購入テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト説明',
            'image' => 'test.jpg',
            'is_sold' => true,
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 1,
        ]);

        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $response->assertSee('購入テスト商品');
    }
    //１１支払い方法選択機能
    public function test_支払い方法が小計画面に反映される()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => '説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'purchase_payment_method_' . $item->id => 1,
                'shipping_address_' . $item->id => [
                    'postal_code' => '123-4567',
                    'address' => 'テスト住所',
                    'building' => null,
                ]
            ])
            ->get('/purchase/' . $item->id);

        $response->assertSee('コンビニ払い');
    }
    // １２配送先変更機能
    public function test_配送先変更が購入画面に反映される()
    {
        $user = User::factory()->create();

        $seller = User::factory()->create();

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => '説明',
            'image' => 'test.jpg',
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'shipping_address_' . $item->id => [
                    'postal_code' => '123-4567',
                    'address' => '東京都渋谷区1-1-1',
                    'building' => 'テストマンション101',
                ]
            ])
            ->get('/purchase/' . $item->id);

        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区1-1-1');
        $response->assertSee('テストマンション101');
    }

    public function test_購入時に配送先住所が保存される()
    {
        $user = User::factory()->create();

        $seller = User::factory()->create();

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => '説明',
            'image' => 'test.jpg',
        ]);

        $this->actingAs($user)
            ->withSession([
                'shipping_address_' . $item->id => [
                    'postal_code' => '123-4567',
                    'address' => '東京都渋谷区1-1-1',
                    'building' => 'テストマンション101',
                ],
                'purchase_payment_method_' . $item->id => 1,
            ])
            ->get('/purchase/' . $item->id . '/success');

        $purchase = Purchase::first();

        $this->assertDatabaseHas('shipping_addresses', [
            'purchase_id' => $purchase->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストマンション101',
        ]);
    }
}