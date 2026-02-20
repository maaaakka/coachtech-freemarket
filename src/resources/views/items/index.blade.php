@extends('layouts.app')


@section('content')

@php
    $conditions = [
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    ];
@endphp

<div class="container">

    {{-- タブ --}}
    <div class="tabs">
        <a
            href="/?tab=recommend&keyword={{ request('keyword') }}"
            class="tab {{ request('tab') !== 'mylist' ? 'active' : '' }}"
        >
            おすすめ
        </a>

        <a
            href="/?tab=mylist&keyword={{ request('keyword') }}"
            class="tab {{ request('tab') === 'mylist' ? 'active' : '' }}"
        >
            マイリスト
        </a>
    </div>

    {{-- 商品一覧 --}}
    <div class="items-grid">

        {{-- 未ログインでマイリストタブなら空表示 --}}
        @if(request('tab') === 'mylist' && !auth()->check())
        @else
            @foreach($items as $item)
                <div class="item-card">
                    <a href="{{ route('items.show', $item->id) }}">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">

                        {{-- Sold表示 --}}
                        @if($item->purchase)
                            <span class="sold-label">Sold</span>
                        @endif
                    </a>
                    <p class="item-name">{{ $item->name }}</p>
                </div>
            @endforeach
        @endif

    </div>
</div>
@endsection