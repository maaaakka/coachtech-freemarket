<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\Address;
use App\Models\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DetailTest extends TestCase
{
    use RefreshDatabase;
    
    // 支払い方法選択機能
    public function test_payment_method_reflected_in_confirm()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user);

    $response = $this->get("/purchase/{$item->id}?payment_method=2");

    $response->assertStatus(200);
    $response->assertSee('コンビニ'); // 表示確認
}

// 住所が購入画面に反映
public function test_changed_address_reflected_in_confirm()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'temp_address' => [
                'postcode' => '111-1111',
                'address' => '東京都テスト',
                'building' => 'テストビル'
            ],
            'temp_address_item_id' => $item->id
        ]);

    $response = $this->get("/purchase/{$item->id}");

    $response->assertSee('東京都テスト');
}


// プロフィールユーザー情報取得
public function test_profile_page_data_displayed()
{
    $user = User::factory()->create([
        'name' => 'テスト太郎'
    ]);

    Item::factory()->count(2)->create(['user_id' => $user->id]);
    Purchase::create([
    'user_id' => $user->id,
    'item_id' => Item::factory()->create()->id,
    'address_id' => Address::factory()->create()->id,
    'payment_method' => 1,
    'payment_status' => 1,
]);

    $response = $this->actingAs($user)->get('/mypage');

    $response->assertSee('テスト太郎');
}

// ユーザー情報変更
public function test_profile_edit_has_initial_values()
{
    $user = User::factory()->create();

    Profile::create([
        'user_id' => $user->id,
        'postcode' => '123-4567',
        'address' => '東京'
    ]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertStatus(200);
    $response->assertSee('123-4567');
    $response->assertSee('東京');
}

// 出品商品登録
public function test_item_can_be_created()
{
    Storage::fake('public');

    $user = User::factory()->create();

    // factory不要
    $category = \App\Models\Category::create(['name' => 'テスト']);

    $response = $this->actingAs($user)->post('/sell', [
        'name' => 'テスト商品',
        'brand' => 'テストブランド',
        'description' => '説明',
        'price' => 1000,
        'condition' => 1,

        // フィールド名
        'image_path' => UploadedFile::fake()->create('test.jpg', 100),

        // 多対多
        'categories' => [$category->id],
    ]);

    $response->assertRedirect(); // 成功確認

    $this->assertDatabaseHas('items', [
        'name' => 'テスト商品',
        'user_id' => $user->id,
    ]);
}
}
