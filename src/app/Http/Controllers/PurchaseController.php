<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * 購入確認画面
     */
    public function confirm(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        /* ======================
        🟢 支払い方法セッション管理
        ====================== */

        // 別商品に来たら支払い方法もリセット
        if (session('payment_item_id') != $item_id) {
            session()->forget(['payment_method', 'payment_item_id']);
        }

        // 選択されたら保存
        if ($request->has('payment_method')) {
            session([
                'payment_method' => $request->payment_method,
                'payment_item_id' => $item_id
            ]);
        }

        $paymentMethod = session('payment_method');


        /* ======================
        🟢 仮住所管理（今のまま）
        ====================== */

        $temp       = session('temp_address');
        $tempItemId = session('temp_address_item_id');

        // 違う商品なら仮住所を消す
        if ($tempItemId != $item_id) {
            session()->forget(['temp_address', 'temp_address_item_id']);
            $temp = null;
        }

        if ($temp) {
            $displayAddress = (object) $temp;
        } else {
            $displayAddress = $user->profile;
        }

        return view('purchase.confirm', compact(
            'item',
            'user',
            'displayAddress',
            'paymentMethod'
        ));
    }

    /**
     * 購入確定処理
     */
    public function store(PurchaseRequest $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // 自分の商品は買えない
        if ($item->user_id === $user->id) {
            return back()->with('error', '自分の商品は購入できません');
        }

        // 売り切れチェック
        if (Purchase::where('item_id', $item->id)->exists()) {
            return back()->with('error', 'この商品はすでに購入されています');
        }

        // 🔥 購入時に使う住所データ決定
        $tempAddress = session('temp_address');

        if ($tempAddress) {
            // 変更した住所を保存
            $address = Address::create([
                'user_id'  => $user->id,
                'postcode' => $tempAddress['postcode'],
                'address'  => $tempAddress['address'],
                'building' => $tempAddress['building'],
            ]);

            // セッション削除（次回はprofileに戻す）
            session()->forget(['temp_address', 'temp_address_item_id']);

        } elseif ($user->profile) {
            // profile住所で購入する場合も address として保存
            $address = Address::create([
                'user_id'  => $user->id,
                'postcode' => $user->profile->postcode,
                'address'  => $user->profile->address,
                'building' => $user->profile->building,
            ]);
        } else {
            return back()->with('error', '住所を登録してください');
        }

        // 購入記録作成
        Purchase::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'address_id'     => $address->id,
            'payment_method' => $request->payment_method,
            'payment_status' => 0,
        ]);

        return redirect()->route('mypage')
            ->with('success', '購入が完了しました');
    }
}