<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Checkout\Session as StripeSession;

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

    public function store()
    {
        return redirect()->back();
    }
    /**
     * 購入確定処理
     */
    public function success(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripeSessionId = $request->query('session_id');

        if (!$stripeSessionId) {
            return redirect()->route('items.index')->with('error', 'セッションIDがありません');
        }

        // Stripeに直接確認（←セッション照合はしない）
        $stripeSession = \Stripe\Checkout\Session::retrieve($stripeSessionId);

        if ($stripeSession->payment_status !== 'paid') {
            return redirect()->route('items.index')->with('error', '支払い未完了');
        }

        // 🔥 Stripeのmetadataから商品ID取得
        $itemId = $stripeSession->metadata->item_id ?? null;

        if (!$itemId) {
            return redirect()->route('items.index')->with('error', '商品情報が取得できません');
        }

        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        // 自分の商品は買えない
        if ($item->user_id === $user->id) {
            return redirect()->route('items.index')->with('error', '自分の商品は購入できません');
        }

        // 売り切れチェック
        if (Purchase::where('item_id', $item->id)->exists()) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています');
        }

        // 🔥 住所決定
        $tempAddress = session('temp_address');

        if ($tempAddress) {
            $address = Address::create([
                'user_id'  => $user->id,
                'postcode' => $tempAddress['postcode'],
                'address'  => $tempAddress['address'],
                'building' => $tempAddress['building'],
            ]);
            session()->forget(['temp_address', 'temp_address_item_id']);

        } elseif ($user->profile) {
            $address = Address::create([
                'user_id'  => $user->id,
                'postcode' => $user->profile->postcode,
                'address'  => $user->profile->address,
                'building' => $user->profile->building,
            ]);
        } else {
            return redirect()->route('items.index')->with('error', '住所を登録してください');
        }

        // 購入記録作成
        Purchase::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'address_id'     => $address->id,
            'payment_method' => session('payment_method', 1),
            'payment_status' => 1,
        ]);

        session()->forget(['payment_method', 'payment_item_id', 'temp_address']);

        return redirect()->route('mypage')->with('success', '購入が完了しました');
    }

    public function checkout(Request $request, Item $item)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card', 'konbini'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            // 🔥 ここが超重要
            'metadata' => [
                'item_id' => $item->id,
                'user_id' => auth()->id(),
            ],
            'success_url' => url('/purchase/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('/purchase/cancel'),
        ]);

        return redirect($session->url);
    }

    public function cancel()
    {
        return redirect()->route('items.index')
            ->with('error', '決済がキャンセルされました');
    }
}