@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-form">
  <h2 class="profile-form__heading">プロフィール設定</h2>

  <div class="profile-form__inner">
    <form class="register-form__form" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
      @csrf

        {{-- プロフィール画像 --}}
        <div class="profile-form__group">
          <label class="profile-form__label">プロフィール画像</label>
          <input type="file" name="image">
            @error('image')
              <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile-form__group">
          <label class="profile-form__label">ユーザー名</label>
          <input class="profile-form__input" type="text" name="name" value="{{ old('name', auth()->user()->name) }}">
            @error('name')
              <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile-form__group">
          <label class="profile-form__label">郵便番号</label>
          <input class="profile-form__input" type="text" name="postal_code" value="{{ old('postal_code', optional(auth()->user()->profile)->postal_code) }}">
            @error('postal_code')
              <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile-form__group">
          <label class="profile-form__label">住所</label>
          <input class="profile-form__input" type="address" name="address" value="{{ old('address', optional(auth()->user()->profile)->address) }}">
          <p class="address-form__error-message">
          @error('password') {{ $message }} @enderror
          </p>
        </div>

      
        <div class="profile-form__group">
          <label class="profile-form__label">建物名</label>
          <input class="profile-form__input" type="text" name="building" value="{{ old('building', optional(auth()->user()->profile)->building) }}">
        </div>

        <button class="profile-form__btn">更新する</button>


    </form>
  </div>
</div>


@endsection