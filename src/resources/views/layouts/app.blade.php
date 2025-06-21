<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'COACHTECH Flema')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    @yield('css')
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="{{ route('home') }}" class="header__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ">
            </a>

            @if (!in_array(request()->path(), ['login', 'register', 'email/verify']))
            <form method="GET" action="{{ route('home') }}" class="header__search-form">
                <input type="text" name="query" placeholder="なにをお探しですか？" value="{{ request('query') }}">
            </form>

            <nav class="header__nav">
                @auth
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('ログアウトしますか？')">
                    @csrf
                    <button type="submit" class="nav__link nav__logout">ログアウト</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="nav__link">ログイン</a>
                @endauth

                <a href="{{ route('mypage') }}" class="nav__link">マイページ</a>
                <a href="{{ route('sell.create') }}" class="nav__link button-sell">出品</a>
            </nav>
            @endif
        </div>
    </header>

    <main class="main">
        @yield('content')
        @yield('js')
    </main>
</body>

</html>