@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-wrapper">

    <div class="auth-card">

        <p class="auth-text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        {{-- 認証はこちらから（メールアプリ開く用） --}}
        <a href="https://mail.google.com" target="_blank" class="auth-main-btn">
            認証はこちらから
        </a>

        {{-- 再送メッセージ --}}
        @if (session('message'))
            <p class="success">{{ session('message') }}</p>
        @endif

        {{-- 再送ボタン --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-sub-link">
                認証メールを再送する
            </button>
        </form>

    </div>

</div>
@endsection