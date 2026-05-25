<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>

<header class="header">
    <div class="header__inner">

        {{-- ヘッダーロゴ：クリックでホーム画面へ --}}
        <a class="header__logo" href="/"><img src="{{ asset('storage/coachtech-logo.png') }}" alt="coachtech"></a>


        {{-- ログイン・会員登録・サンクス画面以外の場合のみ表示 --}}
        @if (!request()->routeIs('login', 'register', 'thanks'))


            {{-- 検索フォーム：キーワードで商品を絞り込む --}}
            <form action="{{ route('items.index') }}" method="GET" class="header__search-form">
                {{-- タブが選択中の場合、タブの状態を維持する --}}
                @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}" class="search">
                @endif
                <input type="text" name="search" placeholder="なにをお探しですか？" class="search" value="{{ request('search') }}">
            </form>

            {{-- ナビゲーション --}}
            <div class="nav">
                @if (Auth::check())
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="link-like">ログアウト</button>
                    </form>
                @else
                    <a class="nav__link" href="{{ route('login') }}">ログイン</a>
                @endif

                <a class="nav__link" href="{{ route('mypage') }}">マイページ</a>
                <a class="nav__btn" href="{{ route('sell') }}">出品</a>
            </div>

        @endif {{-- ログイン・会員登録・サンクス画面以外の条件ここまで --}}

    </div>
</header>

<main>
    @yield('content')
</main>

</body>
</html>