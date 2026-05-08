<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>

<header class="header">
    <div class="header__inner">

        {{-- ヘッダーロゴ：クリックでホーム画面へ --}}
        <a class="header__logo" href="/">COACHTECH</a>

        {{-- ログイン・会員登録・サンクス画面以外の場合のみ表示 --}}
        @if (!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('thanks'))

            {{-- 検索フォーム：キーワードで商品を絞り込む --}}
            <form action="/" method="GET" style="display:contents;">
                {{-- タブが選択中の場合、タブの状態を維持する --}}
                @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                @endif
                <input type="text" name="search" placeholder="なにをお探しですか？" class="search" value="{{ request('search') }}">
            </form>

            {{-- ナビゲーション --}}
            <div class="nav">
                {{-- ログイン中はログアウトボタン、未ログインはログインリンク --}}
                @if (Auth::check())
                    <form action="/logout" method="post" style="display:inline;">
                        @csrf
                        <button type="submit" class="link-like">ログアウト</button>
                    </form>
                @else
                    <a href="/login">ログイン</a>
                @endif

                <a href="/mypage">マイページ</a>
                <a class="btn" href="/sell">出品</a>
            </div>

        @endif {{-- ログイン・会員登録・サンクス画面以外の条件ここまで --}}

    </div>
</header>

<main>
    @yield('content')
</main>

</body>
</html>