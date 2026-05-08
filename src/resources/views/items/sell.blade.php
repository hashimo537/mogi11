@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')

<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>
 
    <form action="/items/sell" method="POST" enctype="multipart/form-data">
        @csrf
 
        {{-- ========================================
             商品画像
        ========================================= --}}
        <div class="sell-section">
            <span class="sell-label">商品画像</span>
 
            <div class="sell-image-area">
                <label for="image-input" class="sell-image-btn">画像を選択する</label>
                <input type="file"
                       name="image"
                       id="image-input"
                       class="sell-image-input"
                       accept="image/*">
            </div>
 
            @error('image')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
 
        {{-- ========================================
             商品の詳細
        ========================================= --}}
        <div class="sell-section">
            <h2 class="sell-section-title">商品の詳細</h2>
 
            {{-- カテゴリー --}}
            <div class="sell-form-item">
                <span class="sell-label">カテゴリー</span>
                <div class="sell-categories">
                    @foreach($categories as $category)
                        <div>
                            <input type="checkbox"
                                   name="categories[]"
                                   id="category-{{ $category->id }}"
                                   value="{{ $category->id }}"
                                   class="sell-category-input"
                                   {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                            <label for="category-{{ $category->id }}" class="sell-category-label">
                                {{ $category->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('categories')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
 
            {{-- 商品の状態 --}}
            <div class="sell-form-item">
                <span class="sell-label">商品の状態</span>
                <select name="condition" class="sell-select">
                    <option value="" disabled selected>選択してください</option>
                    @foreach($conditions as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('condition') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('condition')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>
 
        {{-- ========================================
             商品名と説明
        ========================================= --}}
        <div class="sell-section">
            <h2 class="sell-section-title">商品名と説明</h2>
 
            {{-- 商品名 --}}
            <div class="sell-form-item">
                <label class="sell-label" for="name">商品名</label>
                <input type="text"
                       name="name"
                       id="name"
                       class="sell-input"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
 
            {{-- ブランド名 --}}
            <div class="sell-form-item">
                <label class="sell-label" for="brand">ブランド名</label>
                <input type="text"
                       name="brand"
                       id="brand"
                       class="sell-input"
                       value="{{ old('brand') }}">
                @error('brand')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
 
            {{-- 商品の説明 --}}
            <div class="sell-form-item">
                <label class="sell-label" for="description">商品の説明</label>
                <textarea name="description"
                          id="description"
                          class="sell-textarea">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
 
            {{-- 販売価格 --}}
            <div class="sell-form-item">
                <label class="sell-label" for="price">販売価格</label>
                <div class="sell-price-wrap">
                    <span class="sell-price-prefix">¥</span>
                    <input type="number"
                           name="price"
                           id="price"
                           class="sell-price-input"
                           value="{{ old('price') }}"
                           min="0">
                </div>
                @error('price')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>
 
        <button type="submit" class="sell-submit-btn">出品する</button>
 
    </form>
</div>
 
@endsection
