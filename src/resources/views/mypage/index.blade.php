@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage-index.css') }}">
@endsection


@section('content')
<div class="mypage-container">

    {{-- プロフィール --}}
    <div class="mypage-header">
        @if(optional($user->profile)->image_path)
            <img
                src="{{ asset('storage/' . $user->profile->image_path) }}"
                class="mypage-icon"
                alt="{{ $user->name }}"
            >
        @else
            <div class="mypage-icon default"></div>
        @endif

        <p class="mypage-name">{{ $user->name }}</p>

        <a href="{{ route('profile.edit') }}" class="edit-profile-btn">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ --}}
    <div class="mypage-tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}"
           class="{{ $tab === 'sell' ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="{{ route('mypage', ['page' => 'buy']) }}"
           class="{{ $tab === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    {{-- 商品一覧 --}}
    <div class="item-grid">
        @php
            $items = $tab === 'buy' ? $buyItems : $sellItems;
        @endphp

        @forelse($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}">
                    <img src="{{ asset('storage/' . $item->image_path) }}">
                    <p class="item-name">{{ $item->name }}</p>
                </a>
            </div>
        @empty
            <p>商品がありません</p>
        @endforelse
    </div>

</div>
@endsection