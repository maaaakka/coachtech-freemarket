<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;

use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    // ====================
    // 商品購入機能
    // ====================

    // 購入できる
    public function test_user_can_purchase_item()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'temp_address' => [
                'postcode' => '111',
                'address' => 'テスト住所',
                'building' => null,
            ]
        ])
        ->post("/purchase/{$item->id}", [
            'payment_method' => 1
        ]);

    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'item_id' => $item->id,
    ]);
}

    // sold表示される
    public function test_purchased_item_shows_sold()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 1,
            'payment_status' => 1,
        ]);

        $response = $this->get('/');

        $response->assertSee('Sold');
    }

    // マイページ購入一覧
    public function test_purchased_item_appears_in_profile()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 1,
            'payment_status' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->get('/mypage?tab=buy');

        $response->assertSee($item->name);
    }
}
