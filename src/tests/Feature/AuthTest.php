<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ====================
    // 会員登録
    // ====================

    // 名前未入力
    public function test_register_name_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // メール未入力
    public function test_register_email_required()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // パスワード未入力
    public function test_register_password_required()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'test@test.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // パスワード7文字以下
    public function test_register_password_min_length()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'test@test.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // パスワード不一致
    public function test_register_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => '違う',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // 正常登録
    public function test_register_success()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'ok@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ok@test.com',
        ]);
    }

    // ====================
    // ログイン
    // ====================

    // メール未入力
    public function test_login_email_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // パスワード未入力
    public function test_login_password_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ログイン情報間違い
    public function test_login_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'login@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'login@test.com',
            'password' => 'wrong',
        ]);

        $this->assertGuest();
    }

    // ログイン成功
    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'login@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => 'login@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
    }

    // ====================
    // ログアウト
    // ====================

    public function test_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout');

        $this->assertGuest();
    }
}