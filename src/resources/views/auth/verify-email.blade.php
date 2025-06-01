@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
<div class="verify-container">
    <h1 class="verify-title">登録していただいたメールアドレスに認証メールを送付しました。</h1>
    <p class="verify-subtext">メール認証を完了してください。</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify-button">認証はこちらから</button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify-resend-link">認証メールを再送する</button>
    </form>
</div>
@endsection