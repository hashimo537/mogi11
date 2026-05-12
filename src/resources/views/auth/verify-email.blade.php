@extends('layouts.app')

@section('content')

<div style="
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    text-align: center;
">

    <p style="font-size: 1rem; margin-bottom: 2rem; line-height: 1.8;">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- 認証はこちらからボタン --}}
    <a href="http://localhost:8025" target="_blank" style="
        display: inline-block;
        padding: 16px 48px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #f5f5f5;
        color: #333;
        font-size: 1rem;
        font-weight: bold;
        text-decoration: none;
        margin-bottom: 2rem;
    ">
        認証はこちらから
    </a>

    {{-- 認証メール再送 --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" style="
            background: none;
            border: none;
            color: #4a90e2;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
        ">
            認証メールを再送する
        </button>
    </form>

    {{-- 再送成功メッセージ --}}
    @if (session('status') == 'verification-link-sent')
        <p style="color: green; margin-top: 1rem;">
            認証メールを再送しました。
        </p>
    @endif

</div>

@endsection