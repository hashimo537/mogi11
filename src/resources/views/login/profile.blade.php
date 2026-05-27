@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
  <div class="profile-container">
    <h2 class="profile-title">プロフィール設定</h2>

    <form class="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- プロフィール画像 --}}
      <div class="profile-image-group">
        <div class="profile-image-preview">
          @if(optional(auth()->user()->profile)->image)
            <img src="{{ asset('storage/' . auth()->user()->profile->image) }}" alt="プロフィール画像" id="preview-img">
          @else
            <div class="profile-image-circle" id="preview-img"></div>
          @endif
        </div>
        <label class="profile-image-btn" for="image">
          画像を選択する
        </label>
        <input type="file" name="image" id="image" class="profile-image-input">
        @error('image')
          <p class="error-message">{{ $message }}</p>
        @enderror
      </div>

      {{-- ユーザー名 --}}
      <div class="profile-form__group">
        <label class="profile-form__label" for="name">ユーザー名</label>
        <input class="profile-form__input" type="text" id="name" name="name"
          value="{{ old('name', auth()->user()->name) }}">
        @error('name')
          <p class="error-message">{{ $message }}</p>
        @enderror
      </div>

      {{-- 郵便番号 --}}
      <div class="profile-form__group">
        <label class="profile-form__label" for="postal_code">郵便番号</label>
        <input class="profile-form__input" type="text" id="postal_code" name="postal_code"
          value="{{ old('postal_code', optional(auth()->user()->profile)->postal_code) }}">
        @error('postal_code')
          <p class="error-message">{{ $message }}</p>
        @enderror
      </div>

      {{-- 住所 --}}
      <div class="profile-form__group">
        <label class="profile-form__label" for="address">住所</label>
        <input class="profile-form__input" type="text" id="address" name="address"
          value="{{ old('address', optional(auth()->user()->profile)->address) }}">
        @error('address')
          <p class="error-message">{{ $message }}</p>
        @enderror
      </div>

      {{-- 建物名 --}}
      <div class="profile-form__group">
        <label class="profile-form__label" for="building">建物名</label>
        <input class="profile-form__input" type="text" id="building" name="building"
          value="{{ old('building', optional(auth()->user()->profile)->building) }}">
        @error('building')
          <p class="error-message">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" class="profile-form__btn">更新する</button>

    </form>
  </div>
@endsection