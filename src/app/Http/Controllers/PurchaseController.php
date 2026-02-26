<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;
use Stripe\Webhook;
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

        // GET値を先に取得
$selectedMethod = $request->query('payment_method');

// 送信があった場合だけ保存
if ($selectedMethod !== null) {
    session([
        'payment_method' => $selectedMethod,
        'payment_item_id' => $item_id
    ]);
}

// 別商品に来た時だけリセット
if (
    session('payment_item_id') &&
    session('payment_item_id') != $item_id
) {
    session()->forget(['payment_method', 'payment_item_id']);
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

    public function store(Request $request, $item_id)
{
    $user = Auth::user();
    $item = Item::findOrFail($item_id);

    // 自分の商品購入禁止
    if ($item->user_id === $user->id) {
        return back();
    }

    // 購入済みチェック
    if(
        Purchase::where('item_id', $item->id)
        ->whereIn('payment_status', [
            Purchase::STATUS_PENDING,
            Purchase::STATUS_PAID
        ])
        ->exists()
    ) {
        return back();
    }

    /* =========================
       ⭐ 住所決定ロジック（本命）
    ========================= */

    $addressData = null;

    // ① セッション住所
    if (session('temp_address')) {
        $addressData = session('temp_address');
    }
    // ② プロフィール住所
    elseif ($user->profile) {
        $addressData = [
            'postcode' => $user->profile->postcode,
            'address'  => $user->profile->address,
            'building' => $user->profile->building,
        ];
    }

    if (!$addressData) {
        return back()->with('error', '住所がありません');
    }

    // ⭐ ここで初めて保存（重要）
    $address = Address::create([
        'user_id' => $user->id,
        'postcode' => $addressData['postcode'],
        'address'  => $addressData['address'],
        'building' => $addressData['building'] ?? null,
    ]);

    /* ========================= */

    // 購入作成
    $purchase = Purchase::create([
        'user_id'        => $user->id,
        'item_id'        => $item->id,
        'address_id'     => $address->id,
        'payment_method' => $request->payment_method ?? 1,
        'payment_status' => Purchase::STATUS_PENDING,
    ]);

    session(['purchase_id' => $purchase->id]);

    return redirect()->route('purchase.checkout', $item->id);
}
    /**
     * 購入確定処理
     */
    public function success(Request $request)
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    $sessionId = $request->query('session_id');
    if (!$sessionId) {
        return redirect()->route('items.index')->with('error', 'セッションIDがありません');
    }

    $session = \Stripe\Checkout\Session::retrieve($sessionId);

   

    // ⭐ purchase_idで更新
    $purchaseId = $session->metadata->purchase_id ?? null;

    if ($purchaseId) {
        Purchase::where('id', $purchaseId)
            ->update(['payment_status' => Purchase::STATUS_PAID]);
    }

    return redirect()->route('mypage')->with('success', '購入が完了しました');
}

    public function checkout(Request $request, Item $item)
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    $purchase = Purchase::where('item_id', $item->id)
        ->where('user_id', auth()->id())
        ->latest()
        ->first();

    if (!$purchase) {
        return redirect()->route('items.index')
            ->with('error', '購入情報が見つかりません。');
    }

    // 支払い方法
    $paymentMethods = ['card'];
    $successUrl = route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}';

    // コンビニの場合だけ変更
    if ($purchase->payment_method == 2) {
        $paymentMethods = ['konbini'];
        $successUrl = route('purchase.wait', $item->id);
    }

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => $paymentMethods,
        'payment_method_options' => [
            'konbini' => [
                'expires_after_days' => 3,
            ],
        ],
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
        'metadata' => [
            'purchase_id' => $purchase->id,
        ],
        'success_url' => $successUrl,
        'cancel_url' => route('purchase.cancel'),
    ]);

    return redirect($session->url);
}

public function wait(Item $item)
{
    $purchase = Purchase::where('item_id', $item->id)
        ->where('user_id', auth()->id())
        ->latest()
        ->first();

    return view('purchase.wait', compact('purchase'));
}

    public function webhook(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\Exception $e) {
            return response('Webhook Error', 400);
        }

        $session = $event->data->object;
        $purchaseId = $session->metadata->purchase_id ?? null;

        if (!$purchaseId) {
            return response('No purchase id', 200);
        }

        $purchase = Purchase::find($purchaseId);
        if (!$purchase) {
            return response('Purchase not found', 200);
        }

        switch ($event->type) {

            // 💳 カードのみ完了扱い
            case 'checkout.session.completed':

                if ($purchase->payment_method == Purchase::PAYMENT_CARD) {
                    $purchase->update([
                        'payment_status' => Purchase::STATUS_PAID
                    ]);
                }
                break;

            // 🏪 コンビニ支払い完了
            case 'checkout.session.async_payment_succeeded':
                $purchase->update([
                    'payment_status' => Purchase::STATUS_PAID
                ]);
                break;

            // ⏰ コンビニ期限切れ
            case 'checkout.session.expired':
                $purchase->update([
                    'payment_status' => Purchase::STATUS_EXPIRED
                ]);
                break;

            // ❌ 支払い失敗
            case 'checkout.session.async_payment_failed':
                $purchase->update([
                    'payment_status' => Purchase::STATUS_CANCEL
                ]);
                break;
        }
        return response('OK', 200);
    }

    public function cancel()
    {
        return redirect()->route('items.index')
            ->with('error', '決済がキャンセルされました');
    }

}