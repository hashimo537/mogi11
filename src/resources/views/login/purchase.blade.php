@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

<div class="purchase-container">

    {{-- 左カラム --}}
    <div class="purchase-left">

        {{-- 商品情報 --}}
        <div class="purchase-item">
            <div class="purchase-item-image">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
            </div>
            <div class="purchase-item-info">
                <p class="purchase-item-name">{{ $item->name }}</p>
                <p class="purchase-item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <hr class="divider">

        {{-- 支払い方法 --}}
        <div class="purchase-section">
            <h2 class="purchase-section-title">支払い方法</h2>

            <form id="purchase-form" action="{{ route('purchase.store', $item->id) }}" method="POST">
                @csrf

                <select name="payment_method" id="payment-method" class="payment-select">
                    <option value="" disabled selected>選択してください</option>
                    <option value="1" {{ old('payment_method') == 1 ? 'selected' : '' }}>コンビニ払い</option>
                    <option value="2" {{ old('payment_method') == 2 ? 'selected' : '' }}>カード払い</option>
                </select>

                @error('payment_method')
                    <p class="error-message">{{ $message }}</p>
                @enderror

            </form>
        </div>

        <hr class="divider">

        {{-- 配送先 --}}
        <div class="purchase-section">
            <div class="purchase-section-header">
                <h2 class="purchase-section-title">配送先</h2>
                <a href="{{ route('purchase.address.edit', $item->id) }}" class="change-link">変更する</a>
            </div>

            <div class="shipping-address-box">
                @if ($address)
                    <p>〒 {{ $address->postal_code }}</p>
                    <p>{{ $address->address }}{{ $address->building }}</p>
                @else
                    <p class="no-address">住所が登録されていません。<a href="{{ route('profile.edit') }}">プロフィールから登録してください</a></p>
                @endif
            </div>
        </div>

        <hr class="divider">

    </div>

    {{-- 右カラム：小計・購入ボタン --}}
    <div class="purchase-right">

        <div class="purchase-summary">
            <div class="summary-row">
                <span class="summary-label">商品代金</span>
                <span class="summary-value">¥{{ number_format($item->price) }}</span>
            </div>
            <hr class="summary-divider">
            <div class="summary-row">
                <span class="summary-label">支払い方法</span>
                <span class="summary-value" id="summary-payment">コンビニ払い</span>
            </div>
        </div>

        <button type="submit" form="purchase-form" class="purchase-btn">購入する</button>

    </div>

</div>

@endsection

@push('scripts')
<script>
    // 支払い方法の選択を右カラムに反映
    const select = document.getElementById('payment-method');
    const summaryPayment = document.getElementById('summary-payment');

    const labels = {
        '1': 'コンビニ払い',
        '2': 'カード払い',
    };

    select.addEventListener('change', function () {
        summaryPayment.textContent = labels[this.value] ?? '未選択';
    });

    // 初期表示（バリデーションエラー後の復元）
    if (select.value) {
        summaryPayment.textContent = labels[select.value] ?? '未選択';
    }
</script>
@endpush