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
        @foreach($items as $item)
    <div class="item-card">
        <a href="{{ route('items.show', $item->id) }}">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
        </a>
        <p class="item-name">{{ $item->name }}</p>
    </div>
@endforeach
    </div>

</div>
@endsection