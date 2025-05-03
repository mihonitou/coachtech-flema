<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'COACHTECH Flema')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="{{ route('home') }}" class="header__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ">
            </a>

            @auth
            <form method="GET" action="{{ route('home') }}" class="header__search-form">
                <input type="text" name="query" placeholder="なにをお探しですか？" value="{{ request('query') }}">
            </form>
            <nav class="header__nav">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav__link">ログアウト</button>
                </form>
                <a href="{{ url('/mypage') }}" class="nav__link">マイページ</a>
                <a href="{{ url('/sell') }}" class="nav__link button-sell">出品</a>
            </nav>
            @endauth
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>
</body>

</html>