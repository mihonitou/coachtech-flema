@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
@if (session('success'))
<div class="alert__message">{{ session('success') }}</div>
@endif

<div class="profile-edit-container">
    <h2 class="profile-edit-title">プロフィール設定</h2>

    <form action="{{ route('mypage.edit') }}" method="POST" enctype="multipart/form-data" class="profile-edit-form">
        @csrf

        <!-- プロフィール画像 -->
        <div class="profile-image-wrapper">
            <div class="profile-image-circle">
                {{-- プロフィール画像があれば表示、なければデフォルト --}}
                @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile-image">
                @else
                <div class="default-profile-icon"></div>
                @endif
            </div>
            <label for="profile_image" class="image-select-button">画像を選択する</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*" hidden>
        </div>

        <!-- ユーザー名 -->
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" id="name" name="name" placeholder="ユーザー名を入力" value="{{ old('name', $user->name) }}" class="input-field">
            @error('name')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- 郵便番号 -->
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" placeholder="郵便番号を入力" value="{{ old('postal_code', $user->postal_code) }}" class="input-field">
            @error('postal_code')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- 住所 -->
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" placeholder="住所を入力" value="{{ old('address', $user->address) }}" class="input-field">
            @error('address')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- 建物名 -->
        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" placeholder="建物名を入力" value="{{ old('building', $user->building) }}" class="input-field">
            @error('building')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection