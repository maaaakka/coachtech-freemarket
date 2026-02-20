<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('css')
</head>
<body>

<header class="header">
    <div class="header-inner">

        {{-- ロゴ（常に表示） --}}
        <div class="logo">
            <a href="/">
                <img src="{{ asset('images/COACHTECHlogo.png') }}" alt="COACHTECH">
            </a>
        </div>

        {{-- ▼ ログイン・登録画面ではロゴのみ --}}
        @if (Route::is('login') || Route::is('register'))

            {{-- 何も表示しない --}}

        @else

            {{-- ▼ 検索フォーム --}}
        <form class="search-form" method="GET" action="/">
            <input
                type="text"
                name="keyword"
                placeholder="なにをお探しですか？"
                value="{{ request('keyword') }}"
            >
            <input type="hidden" name="tab" value="{{ request('tab') }}">
        </form>

            {{-- ▼ ナビゲーション --}}
            <nav class="nav">

                {{-- 未ログイン --}}
                @guest
                    <a href="{{ route('login') }}">ログイン</a>
                    <a href="/mypage">マイページ</a>
                    <a href="/sell" class="sell-btn">出品</a>
                @endguest

                {{-- ログイン済み --}}
                @auth

                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-link logout-btn">ログアウト</button>
                    </form>

                    <a href="/mypage" class="nav-link">マイページ</a>
                    <a href="/sell" class="sell-btn">出品</a>

                @endauth

            </nav>

        @endif
    </div>
</header>

<main>
    @yield('content')
</main>


</body>
</html>