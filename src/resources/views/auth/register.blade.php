@extends('layouts.app')

@section('content')
<div class="auth-container">

    <h2>会員登録</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- ユーザー名 --}}
        <div class="form-group">
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- メールアドレス --}}
        <div class="form-group">
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-group">
            <label>パスワード</label>
            <input type="password" name="password">
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 確認用パスワード --}}
        <div class="form-group">
            <label>確認用パスワード</label>
            <input type="password" name="password_confirmation">
        </div>

        <button type="submit">登録する</button>
    </form>

    <p class="auth-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </p>

</div>
@endsection