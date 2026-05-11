@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">

    <h2 class="purchase-title">住所の変更</h2>

    <form action="{{ route('purchase.address.update', $item->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input
                type="text"
                id="postal_code"
                name="postal_code"
                class="form-input"
                value="{{ old('postal_code', $address->postal_code ?? '') }}"
                placeholder="123-4567"
            >
            @error('postal_code')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input
                type="text"
                id="address"
                name="address"
                class="form-input"
                value="{{ old('address', $address->address ?? '') }}"
                placeholder="例：東京都渋谷区..."
            >
            @error('address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input
                type="text"
                id="building"
                name="building"
                class="form-input"
                value="{{ old('building', $address->building ?? '') }}"
                placeholder="例：〇〇マンション101号室（任意）"
            >
            @error('building')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="address-btn">更新する</button>



@endsection