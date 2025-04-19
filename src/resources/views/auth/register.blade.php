@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="header__wrap">
    <h2 class="header__text">会員登録</h2>
</div>

<!-- バリデーションエラー表示 -->
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label for="name">ユーザー名</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required>
    </div>

    <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>
    </div>

    <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div class="form-group">
        <label for="password_confirmation">確認用パスワード</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-register">登録する</button>
    </div>

    <div class="form-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
</form>
@endsection