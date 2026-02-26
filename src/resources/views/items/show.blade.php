@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="item-show-container">

    {{-- 上段：画像＋商品情報 --}}
    <div class="item-main">

        {{-- 左：商品画像 --}}
        <div class="item-image">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="商品画像">
        </div>

        {{-- 右：商品情報 --}}
        <div class="item-detail">

            {{-- 商品名 --}}
            <h1 class="item-name">
                {{ $item->name }}
            </h1>

            {{-- ブランド名 --}}
            <div class="item-brand">
                <span class="brand-label">ブランド名</span>
            @if($item->brand)
                <span class="brand-value">{{ $item->brand}}</span>
            @endif
            </div>

            {{-- 価格 --}}
            <p class="item-price">
                <span class="price-mark">¥</span>
                <span class="price-value">{{ number_format($item->price) }}</span>
                <span class="price-tax">（税込）</span>
            </p>

    {{-- いいね・コメント数 --}}
<div class="item-icons">

@php
    $liked = auth()->check() && $item->likes->where('user_id', auth()->id())->count() > 0;
@endphp

<div class="like-area">

    {{-- ❤️ ログイン済み --}}
    @auth
        @if($liked)
            <form action="{{ route('like.destroy', $item) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="like-btn">
                    <svg class="icon-heart liked" viewBox="0 0 24 24">
                        <path d="M12 21s-6.5-4.35-9-8.28C1.2 10.2 2.4 6.5 6 6.5c2 0 3.1 1.2 4 2.3.9-1.1 2-2.3 4-2.3 3.6 0 4.8 3.7 3 6.22C18.5 16.65 12 21 12 21z"/>
                    </svg>
                    <span>{{ $item->likes->count() }}</span>
                </button>
            </form>
        @else
            <form action="{{ route('like.store', $item) }}" method="POST">
                @csrf
                <button type="submit" class="like-btn">
                    <svg class="icon-heart" viewBox="0 0 24 24">
                        <path d="M12 21s-6.5-4.35-9-8.28C1.2 10.2 2.4 6.5 6 6.5c2 0 3.1 1.2 4 2.3.9-1.1 2-2.3 4-2.3 3.6 0 4.8 3.7 3 6.22C18.5 16.65 12 21 12 21z"/>
                    </svg>
                    <span>{{ $item->likes->count() }}</span>
                </button>
            </form>
        @endif
    @endauth

    {{-- 🔒 未ログイン --}}
    @guest
        <a href="{{ route('login') }}" class="like-btn">
            <svg class="icon-heart" viewBox="0 0 24 24">
                <path d="M12 21s-6.5-4.35-9-8.28C1.2 10.2 2.4 6.5 6 6.5c2 0 3.1 1.2 4 2.3.9-1.1 2-2.3 4-2.3 3.6 0 4.8 3.7 3 6.22C18.5 16.65 12 21 12 21z"/>
            </svg>
            <span>{{ $item->likes->count() }}</span>
        </a>
    @endguest

</div>
    {{-- コメントアイコン --}}
    <div class="comment-icon">
        <svg viewBox="0 0 24 24" class="icon-comment">
            <path d="M21 6h-18v12h4v4l4-4h10z"/>
        </svg>
        <span>{{ $item->comments->count() }}</span>
    </div>

</div>

    {{-- 購入ボタン --}}
    <div class="purchase-button">
        @if($item->purchase)
            <div class="sold-label">Sold</div>
        @else
            @guest
                {{-- 未ログイン --}}
                <a href="{{ route('login') }}" class="btn-purchase">
                    購入手続きへ
                </a>
            @else
                @if($item->user_id === auth()->id())
                    {{-- 自分の商品（見た目同じ・押せない） --}}
                    <a class="btn-purchase no-click">
                        購入手続きへ
                    </a>
                @else
                    <a href="{{ route('purchase.confirm', $item->id) }}" class="btn-purchase">
                        購入手続きへ
                    </a>
                @endif
            @endguest
        @endif
    </div>

    {{-- 商品説明 --}}
    <div class="item-description">
        <h2>商品説明</h2>
        <p>
        {{ $item->description }}
        </p>
    </div>

    {{-- 商品の情報 --}}
<div class="item-info">
    <h2>商品の情報</h2>

    <div class="info-row">
        <span class="label">カテゴリー</span>

        <div class="category-list">
            @foreach($item->categories as $category)
                <span class="category-tag">
                    {{ $category->name }}
                </span>
            @endforeach
        </div>
    </div>

    <div class="info-row">
        <span class="label">商品の状態</span>
        <span class="value">
            {{ config('conditions.' . $item->condition) }}
        </span>
    </div>
</div>

    {{-- コメント一覧 --}}
    <div class="comments">
        <h3>
            コメント（{{ $item->comments->count() }}）
        </h3>

        @forelse($item->comments as $comment)
            <div class="comment">

    {{-- ユーザー名＋アイコン --}}
    <p class="user">
        <span class="comment-user-icon-wrapper">
            @if(optional($comment->user->profile)->image_path)
                <img
                    class="comment-user-icon"
                    src="{{ asset('storage/' . $comment->user->profile->image_path) }}"
                    alt="{{ $comment->user->name }}"
                >
            @else
                <span class="comment-user-icon default"></span>
            @endif
        </span>

        {{ $comment->user->name }}
            </p>

            {{-- コメント本文 --}}
                <p class="comment-body">
                    {{ $comment->body }}
                </p>
                </div>
            @empty
                <div class="no-comment"></div>
            @endforelse
        </div>

{{-- コメント投稿 --}}
<div class="comment-form">
    <h3>商品へのコメント</h3>

    @auth
        {{-- ログイン中 --}}
        <form action="{{ route('comment.store', $item) }}" method="POST">
            @csrf

            <textarea name="body" rows="6">{{ old('body') }}</textarea>

            @error('body')
                <p style="color:red;">{{ $message }}</p>
            @enderror

            <button type="submit">コメントを送信する</button>
        </form>
    @endauth

    @guest
        {{-- 未ログイン --}}
        <form action="{{ route('login') }}" method="GET">
            <textarea rows="6"></textarea>

            <button type="submit">コメントを送信する</button>
        </form>
    @endguest
</div>
@endsection