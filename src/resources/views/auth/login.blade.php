@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="header__wrap">
    <h2 class="header__text">ログイン</h2>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-login">ログインする</button>
    </div>

    <div class="form-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</form>
@endsection