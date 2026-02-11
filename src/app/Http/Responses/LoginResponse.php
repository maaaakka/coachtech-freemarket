<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        // ① メール未認証
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // ② 認証済み・プロフィール未完了
        if (! $user->profile_completed) {
            return redirect()->route('profile.edit');
        }

        // ③ 完了済み
        return redirect('/');
    }
}