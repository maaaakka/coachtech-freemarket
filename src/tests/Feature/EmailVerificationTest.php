<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 会員登録後、認証メールが送信される
     */
    public function test_verification_email_is_sent_after_register()
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        // 認証メール送信
        $user->sendEmailVerificationNotification();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    /**
     * ② 認証導線 → 認証ページへ遷移
     */
    public function test_verification_notice_page_can_be_displayed()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertSee('認証');
    }

    /**
     * ③ 認証完了 → プロフィールへ遷移
     */
    public function test_user_can_verify_email_and_redirect_to_profile()
    {
        $user = User::factory()->unverified()->create();

        // 署名付きURL生成（Laravel公式やり方）
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // 認証後のリダイレクト先チェック
        $response->assertRedirect(route('profile.edit'));
    }
}