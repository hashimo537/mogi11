@extends('layouts.app')

@section('content')
<div class="verify-container">

    <h2>メール認証のお願い</h2>

    <p>登録いただいたメールアドレスに認証メールを送信しました。<br>
    メール内のリンクをクリックして認証を完了してください。</p>

    {{-- 再送信ボタン --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-resend">
            認証メールを再送する
        </button>
    </form>

    {{-- 認証リンクへのボタン --}}
    <a href="https://localhost:8025" class="btn-verify">
        認証はこちらから（Mailhog）
    </a>

    @if (session('status') == 'verification-link-sent')
        <p class="success-message">認証メールを再送しました。</p>
    @endif

</div>
@endsection