@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">

    <h2>住所の変更</h2>

    <form action="{{ route('address.update', $item->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postcode"
                value="{{ old('postcode', optional($address)->postcode) }}">
            @error('postcode')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address"
                value="{{ old('address', optional($address)->address) }}">
            @error('address')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building"
                value="{{ old('building', optional($address)->building) }}">
        </div>

        <button type="submit" class="profile-submit">
            更新する
        </button>
    </form>
</div>
@endsection