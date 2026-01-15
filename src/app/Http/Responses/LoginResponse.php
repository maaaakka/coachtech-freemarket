<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // プロフィール未完了ならプロフィール画面へ
        if (! auth()->user()->profile_completed) {
            return redirect('/profile');
        }

        // 完了済みならトップページへ
        return redirect('/');
    }
}