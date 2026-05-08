@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">

  {{-- プロフィール --}}
  <div class="mypage__header">
    <div class="mypage__profile">

      {{-- プロフィール画像 --}}
      <div class="mypage__image">
        @if(optional($user->profile)->image)
          <img src="{{ asset('storage/' . $user->profile->image) }}" alt="{{ $user->name }}のプロフィール画像">
        @else
          <div class="default-circle"></div>
        @endif
      </div>

      {{-- ユーザー名 --}}
      <h2 class="mypage__name">
        {{ $user->name }}
      </h2>

      {{-- 編集ボタン --}}
      <a href="/mypage/profile" class="mypage__edit-btn">
        プロフィールを編集
      </a>

    </div>
  </div>

  {{-- タブ --}}
  <div class="mypage__tabs">
    <a href="/mypage?page=sell" class="{{ request('page') !== 'buy' ? 'active' : '' }}">
      出品した商品
    </a>
    <a href="/mypage?page=buy" class="{{ request('page') === 'buy' ? 'active' : '' }}">
      購入した商品
    </a>
  </div>

  {{-- 商品一覧 --}}
  <div class="mypage__items">

    @if(request('page') === 'buy')
      {{-- 購入商品 --}}
      @forelse ($buyItems as $purchase)
        <div class="item">
          <img src="{{ asset('storage/' . $purchase->item->image) }}" alt="{{ $purchase->item->name }}">
          <p>{{ $purchase->item->name }}</p>
        </div>
      @empty
        <p>購入した商品はありません。</p>
      @endforelse

    @else
      {{-- 出品商品 --}}
      @forelse ($sellItems as $item)
        <div class="item">
          <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
          <p>{{ $item->name }}</p>
        </div>
      @empty
        <p>出品した商品はありません。</p>
      @endforelse
    @endif

  </div>

</div>
@endsection