<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;

class InteractionTest extends TestCase
{
    use RefreshDatabase;
    // ====================
    // いいね機能
    // ====================

    // いいね登録
    public function test_user_can_like_item()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/like/{$item->id}");

        $response->assertStatus(302);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    // いいね数表示
    public function test_like_count_increases()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);
        $this->post("/like/{$item->id}");

        $response = $this->get("/item/{$item->id}");

        $response->assertSee('1'); // 表示形式に合わせて変更OK
    }

    // いいね解除
    public function test_user_can_unlike_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'delete_flag' => 0,
        ]);

        $this->actingAs($user);

        $this->delete("/like/{$item->id}");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'delete_flag' => 1,
        ]);
    }


    // ====================
    // コメント機能
    // ====================

    // ログインユーザーはコメントできる
    public function test_user_can_comment()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post("/comment/{$item->id}", [
            'body' => 'テストコメント'
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'テストコメント',
        ]);
    }

    // 未ログインはコメントできない
    public function test_guest_cannot_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/comment/{$item->id}", [
            'body' => 'ゲストコメント'
        ]);

        $response->assertRedirect('/login');
    }

    // 空コメントバリデーション
    public function test_comment_required_validation()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/comment/{$item->id}", [
            'body' => ''
        ]);

        $response->assertSessionHasErrors('body');
    }

    // ２５５文字以下バリデーション
    public function test_comment_max_length_validation()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $long = str_repeat('あ', 256);

        $response = $this->post("/comment/{$item->id}", [
            'body' => $long
        ]);

        $response->assertSessionHasErrors('body');
    }
}