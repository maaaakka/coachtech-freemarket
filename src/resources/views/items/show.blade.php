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
            <p class="item-brand">
                ブランド名
            @if($item->brand)
                <p class="item-brand">{{ $item->brand }}</p>
            @endif
            </p>

            {{-- 価格 --}}
            <p class="item-price">
                ¥{{ number_format($item->price) }}（税込）
            </p>

            {{-- いいね・コメント数 --}}
            <div class="item-icons">

            @auth
    @php
        $liked = $item->likes->where('user_id', auth()->id())->count() > 0;
    @endphp

    <div class="like-area">
        @if($liked)
            <form action="{{ route('like.destroy', $item) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">
                    ❤️ {{ $item->likes->count() }}
                </button>
            </form>
        @else
            <form action="{{ route('like.store', $item) }}" method="POST">
                @csrf
                <button type="submit">
                    ♡ {{ $item->likes->count() }}
                </button>
            </form>
        @endif
    </div>
@endauth

        <p>💬 {{ $item->comments->count() }}</p>
        </div>

    {{-- 購入ボタン --}}
    <div class="purchase-button">
        <a href="{{ route('purchase.confirm', $item->id) }}" class="btn-purchase">
            購入手続きへ
        </a>
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
            <span class="value">
                @foreach($item->categories as $category)
                <span class="category-tag">
                    {{ $category->name }}
                </span>
                @endforeach
            </span>
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
        <p>こちらにコメントが入ります。</p>
    @endforelse
</div>

{{-- コメント投稿 --}}
@auth
    <div class="comment-form">
        <h3>商品へのコメント</h3>

        <form action="{{ route('comment.store', $item) }}" method="POST">
            @csrf

            <textarea name="body" rows="6">{{ old('body') }}</textarea>

            @error('body')
                <p style="color:red;">{{ $message }}</p>
            @enderror

            <button type="submit">コメントを送信する</button>
        </form>
    </div>
@endauth
</div>
@endsection