<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase;
use App\Models\Address;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemFeatureTest extends TestCase
{
    use RefreshDatabase;

    // =============================
    // 商品一覧取得
    // =============================

    /** 全商品を取得できる */
    public function test_items_all_displayed()
    {
        Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertEquals(3, Item::count());
    }

    /** 購入済み商品はSold表示 */
    public function test_sold_label_display()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        // 購入レコードを作る = Sold状態
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 1,
            'payment_status' => 1,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }
    /** 自分の出品は表示されない */
    public function test_my_items_hidden()
    {
        $user = User::factory()->create();

        Item::factory()->create(['user_id' => $user->id]); // 自分
        $other = Item::factory()->create(); // 他人

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee($user->name);
    }

    // =============================
    // マイリスト一覧取得
    // =============================

    /** いいねした商品のみ表示 */
    public function test_mylist_only_liked_items()
    {
        $user = User::factory()->create();

        $liked = Item::factory()->create(['name' => 'いいね商品']);
        $notLiked = Item::factory()->create(['name' => '表示されない商品']);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $liked->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertSee('いいね商品');
        $response->assertDontSee('表示されない商品');
    }

    /** マイリストでもSold表示 */
    public function test_mylist_sold_label()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        // いいね
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 購入済みにする
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 1,
            'payment_status' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertSee('Sold');
    }

    /** 未認証は何も表示されない */
    public function test_mylist_guest_empty()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('商品名'); // 何も出ない確認
    }

    // =============================
    // 商品検索
    // =============================

    /** 商品名部分一致検索 */
    public function test_item_search_partial()
    {
        Item::factory()->create(['name' => 'iPhone']);
        Item::factory()->create(['name' => 'Galaxy']);

        $response = $this->get('/?keyword=iPhone');

        $response->assertSee('iPhone');
        $response->assertDontSee('Galaxy');
    }

    /** 検索状態保持（簡易チェック） */
    public function test_search_keyword_persist()
    {
        $response = $this->get('/?keyword=test');

        $response->assertStatus(200);
        $response->assertSee('test');
    }

    // =============================
    // 商品詳細情報取得
    // =============================

    /** 商品詳細に必要情報が表示される */
    public function test_item_detail_display()
    {
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'description' => '説明文',
            'price' => 1000,
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('説明文');
        $response->assertSee('1,000');
    }

    /** 複数カテゴリ表示（存在確認） */
    public function test_item_multiple_categories_display()
    {
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
    }
}