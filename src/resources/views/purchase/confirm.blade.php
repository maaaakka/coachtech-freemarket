@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

<div class="purchase-container">

    {{-- 左エリア --}}
    <div class="purchase-left">

        {{-- 商品情報 --}}
        <div class="item-area">
            <div class="item-image">
                <img src="{{ asset('storage/' . $item->image_path) }}">
            </div>
            <div class="item-info">
                <h3 class="item-name">{{ $item->name }}</h3>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <div class="divider"></div>

        {{-- 🟢 支払い方法（別フォーム） --}}
        <div class="section">
            <div class="section-header">
                <h4>支払い方法</h4>
            </div>

            <div class="section-body">
                <form method="GET" action="{{ route('purchase.confirm', $item->id) }}">
                    <select name="payment_method" onchange="this.form.submit()" required>
                        <option value="">選択してください</option>
                        <option value="1" {{ $paymentMethod == 1 ? 'selected' : '' }}>クレジットカード</option>
                        <option value="2" {{ $paymentMethod == 2 ? 'selected' : '' }}>コンビニ払い</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="divider"></div>

        {{-- 配送先 --}}
        <div class="section">
            <div class="section-header">
                <h4>配送先</h4>
                <a href="{{ route('address.edit', $item->id) }}">変更する</a>
            </div>
            <div class="section-body">
                @if($displayAddress)
                    <p>〒{{ $displayAddress->postcode }}</p>
                    <p>{{ $displayAddress->address }}</p>
                    <p>{{ $displayAddress->building }}</p>
                @else
                    <p class="error">住所を登録してください</p>
                @endif
            </div>
        </div>

    </div>


    {{-- 🟢 右エリア（購入フォーム） --}}
    <div class="purchase-right">
    <form action="{{ route('purchase.checkout', $item) }}" method="POST">
    @csrf

            <div class="summary">
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row">
                    <span>支払い方法</span>
                    <span>
                        @if($paymentMethod == 1)
                            クレジットカード
                        @elseif($paymentMethod == 2)
                            コンビニ払い
                        @else
                            未選択
                        @endif
                    </span>
                </div>
            </div>

            {{-- sessionの支払い方法を送る --}}
            <input type="hidden" name="payment_method" value="{{ $paymentMethod }}">

            <button class="purchase-btn" {{ !$displayAddress ? 'disabled' : '' }}>
                購入する
            </button>
        </form>
    </div>

</div>
@endsection