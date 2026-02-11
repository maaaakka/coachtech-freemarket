@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-container">

    <h2 class="login-title">ログイン</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

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

        <button type="submit">ログインする</button>
    </form>

    <p class="auth-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>

</div>
@endsection