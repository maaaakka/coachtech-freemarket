@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">

    <h2 class="profile-title">プロフィール設定</h2>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- アイコン --}}
        <div class="profile-image-area">

            @if(optional($user->profile)->image_path)
                <img src="{{ asset('storage/' . optional($user->profile)->image_path) }}" class="profile-image">
            @else
                <div class="profile-image default"></div>
            @endif

            <label for="image_path" class="image-select-btn">画像を選択する</label>
            <input type="file" id="image_path" name="image_path" accept="image/*" class="file-input">

            @error('image_path')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- ユーザー名 --}}
        <div class="form-group">
            <label>ユーザー名</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}" maxlength="255" required
            >

            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="form-group">
            <label>郵便番号</label>
            <input
                type="text"
                name="postcode" pattern="\d{3}-\d{4}" 
                value="{{ old('postcode', optional($user->profile)->postcode) }}" inputmode="numeric" required
            >

            @error('postcode')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="form-group">
            <label>住所</label>
            <input
                type="text"
                name="address"
                value="{{ old('address', optional($user->profile)->address) }}" maxlength="255" required
            >

            @error('address')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="form-group">
            <label>建物名</label>
            <input
                type="text"
                name="building"
                value="{{ old('building', optional($user->profile)->building) }}"
            >

            @error('building')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="profile-submit">
            更新する
        </button>
    </form>
</div>
@endsection
