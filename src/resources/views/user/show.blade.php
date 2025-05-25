<!-- resources/views/user/show.blade.php -->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user_show.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <div class="profile-header">
        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile-image">
        <div class="profile-name">{{ $user->name }}</div>
        <a href="{{ route('profile.edit') }}" class="edit-button">プロフィールを編集</a>
    </div>

    <div class="tab-menu">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab-link {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab-link {{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="items-container">
        @if ($tab === 'sell')
        @foreach ($listedItems as $item)
        <div class="item-card">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="商品画像" class="item-image">
            <div class="item-name">{{ $item->name }}</div>
        </div>
        @endforeach
        @else
        @foreach ($purchasedItems as $item)
        <div class="item-card">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="商品画像" class="item-image">
            <div class="item-name">{{ $item->name }}</div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection