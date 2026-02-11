<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('profile');

        return view('auth.profile', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

       // プロフィール作成 or 更新
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postcode' => $request->postcode,
                'address'  => $request->address,
                'building' => $request->building,
            ]
        );

        // 画像アップロード
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('profiles', 'public');
            $profile->update([
                'image_path' => $path,
            ]);
        }

        // users テーブル更新
        $user->update([
            'name' => $request->name,
            'profile_completed' => true,
        ]);

        // 🔥 メール未認証なら認証画面へ戻す
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        return redirect('mypage');
    }
}