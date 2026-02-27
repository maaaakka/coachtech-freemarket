<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;

class MypageController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('profile');

        // 出品した商品
        $sellItems = Item::where('user_id', $user->id)
            ->latest()
            ->get();

        // 購入した商品（purchasesテーブルで判定）
        $buyItems = Item::whereHas('purchase', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->latest()
        ->get();

        $tab = request('page', 'sell');

        return view('mypage.index', compact(
            'user',
            'sellItems',
            'buyItems',
            'tab'
        ));
    }
}