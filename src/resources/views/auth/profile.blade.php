@extends('layouts.app')

@section('content')
<div class="profile-container">
    <h2>プロフィール設定</h2>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- プロフィール画像 --}}
        <div>
            <img src="{{ auth()->user()->profile_image 
                ? asset('storage/' . auth()->user()->profile_image) 
                : asset('images/default.png') }}" width="100">

            <input type="file" name="profile_image">
        </div>

        {{-- ユーザー名 --}}
        <div>
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}">
            @error('name') <p>{{ $message }}</p> @enderror
        </div>

        {{-- 郵便番号 --}}
        <div>
            <label>郵便番号</label>
            <input type="text" name="postcode" value="{{ old('postcode', auth()->user()->postcode ?? '') }}">
            @error('postcode') <p>{{ $message }}</p> @enderror
        </div>

        {{-- 住所 --}}
        <div>
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', auth()->user()->address ?? '') }}">
            @error('address') <p>{{ $message }}</p> @enderror
        </div>

        {{-- 建物名 --}}
        <div>
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', auth()->user()->building ?? '') }}">
            @error('building') <p>{{ $message }}</p> @enderror
        </div>

        <button type="submit">更新する</button>
    </form>
</div>
@endsection