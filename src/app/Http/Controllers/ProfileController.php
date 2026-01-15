<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('auth.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'postcode' => ['required'],
            'address' => ['required'],
            'profile_image' => ['nullable', 'image'],
        ]);

        $user = Auth::user();

        // 画像アップロード
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        // ユーザー更新
        $user->update([
            'name' => $request->name,
            'profile_completed' => 1,
        ]);

        // 住所更新 or 作成
        Address::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect('/')->with('success', 'プロフィールを更新しました');
    }
}
