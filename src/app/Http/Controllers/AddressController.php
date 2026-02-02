<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Models\Item;

class AddressController extends Controller
{
    /**
     * 住所変更画面表示
     */
    public function edit($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // 🔥 セッションに一時住所があればそれを優先
        $temp = session('temp_address');

        return view('purchase.address.edit', [
            'item' => $item,
            'address' => $temp ?? $user->profile // デフォルトはprofile
        ]);
    }

    /**
     * 住所変更「更新」ボタン
     * ※まだDB保存しない！！
     */
    public function update(AddressRequest $request, $item_id)
    {
        // 商品ごとの仮住所として保存する
        session([
            'temp_address' => [
                'postcode' => $request->postcode,
                'address'  => $request->address,
                'building' => $request->building,
            ],
            'temp_address_item_id' => $item_id, // ← これが超重要
        ]);

        return redirect()->route('purchase.confirm', $item_id);
    }
}