@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
    <div class="verify-container">

        <p class="verify-message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        {{-- Mailhogへのリンク --}}
        <a href="{{ config('app.mailhog_url', 'http://localhost:8025') }}" target="_blank" class="verify-btn">
            認証はこちらから
        </a>

        {{-- 認証メール再送 --}}
        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit" class="verify-resend-btn">
                認証メールを再送する
            </button>
        </form>

        {{-- 再送成功メッセージ --}}
        @if (session('status') == 'verification-link-sent')
            <p class="verify-success">認証メールを再送しました。</p>
        @endif

    </div>
@endsection