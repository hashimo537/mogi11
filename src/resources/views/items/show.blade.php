@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection


@section('content')

<div class="item-detail">

    {{-- 左：商品画像 --}}
    <div class="item-image">
        <img src="{{ asset('storage/' . $item->image) }}" alt="">

        @if ($item->is_sold)
            <span class="sold">Sold</span>
        @endif
    </div>

    {{-- 右：商品情報 --}}
    <div class="item-info">

        {{-- 商品名 --}}
        <h1 class="item-name">{{ $item->name }}</h1>

        {{-- ブランド名（仮） --}}
        <p class="brand-name">{{ $item->brand ?? '未設定' }}</p>

        {{-- 価格 --}}
        <p class="price">¥{{ number_format($item->price) }} <span>(税込)</span></p>

        {{-- いいね・コメント数 --}}
        <div class="item-meta">

        {{-- いいね --}}
        @auth
        <form action="{{ route('like.toggle', $item->id) }}" method="POST" class="like-form">
            @csrf
            <button type="submit" class="like-btn">
                @if ($item->likedUsers->contains(auth()->id()))
                    <img src="{{ asset('storage/ハートロゴ_ピンク.png') }}" alt="いいね済み" class="like-icon">
                @else
                    <img src="{{ asset('storage/ハートロゴ_デフォルト.png') }}" alt="いいね" class="like-icon">
                @endif
                <span>{{ $item->likedUsers->count() }}</span>
            </button>
        </form>
    @else
        {{-- 未ログインは表示のみ --}}
        <span class="like-display">
            <img src="{{ asset('storage/ハートロゴ_デフォルト.png') }}" alt="いいね" class="like-icon">
            <span>{{ $item->likedUsers->count() }}</span>
        </span>
    @endauth

    {{-- コメント数 --}}
    <span class="comment-display">
        <img src="{{ asset('storage/ふきだしロゴ.png') }}" alt="コメント" class="comment-icon">
        <span>{{ $item->comments->count() }}</span>
    </span>

</div>

        {{-- 購入ボタン --}}
        <a class="purchase-btn" href="{{ route('purchase.show', $item->id) }}" >購入手続きへ</a>

        {{-- 商品説明 --}}
        <div class="description">
            <h2>商品説明</h2>

            <p>カラー：{{ $item->color ?? '未設定' }}</p>

            <p>{{ $item->description }}</p>
        </div>

        {{-- 商品情報 --}}
        <div class="item-detail-info">
            <h2>商品の情報</h2>

            {{-- 複数カテゴリ --}}
            <p>カテゴリー：
            <span class="category">
                {{ $item->categories->pluck('name')->join(', ') ?? '未設定' }}
            </span>
            </p>

            <p>商品の状態：{{ $item->condition_label }}</p>

        </div>

        {{-- コメント一覧 --}}
<div class="comments">
    <h2>コメント ({{ $item->comments->count() }})</h2>

    @foreach ($item->comments as $comment)
        <div class="comment">
            <div class="comment-user">
                {{ $comment->user->name }}
            </div>
            <div class="comment-body">
            {{ $comment->comment }}  
</div>
        </div>
    @endforeach
</div>

{{-- コメント投稿（ログインユーザーのみ） --}}
@auth
<div class="comment-form">
    <h2>商品へのコメント</h2>

    <form action="{{ route('comment.store', $item->id) }}" method="POST">
        @csrf
        <textarea name="comment" rows="4" placeholder="コメントを入力してください"></textarea>
        <button type="submit">コメントを送信する</button>
    </form>
</div>
@else
<div class="comment-form">
    <p><a href="{{ route('login') }}">ログイン</a>するとコメントできます。</p>
</div>
@endauth


    </div>
</div>

@endsection