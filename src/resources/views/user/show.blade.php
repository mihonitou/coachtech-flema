<!-- resources/views/user/show.blade.php -->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user_show.css') }}">
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="mypage-container">
    <div class="profile-header">
        @if ($user->profile_image)
        <img src="{{ Str::startsWith($user->profile_image, 'http') ? $user->profile_image : asset('storage/' . $user->profile_image) }}"
            alt="プロフィール画像" class="profile-image">
        @else
        <img src="{{ asset('images/default_profile.png') }}" alt="デフォルト画像" class="profile-image">
        @endif

        <div class="profile-name">{{ $user->name }}</div>
        <a href="{{ route('mypage.edit') }}" class="edit-button">プロフィールを編集</a>
    </div>

    <div class="tab-menu">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab-link {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab-link {{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="items-container">
        @if ($tab === 'sell')
        @foreach ($listedItems as $item)
        <div class="item-card">
            <a href="{{ route('items.show', $item) }}">
                <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                    alt="{{ $item->name }}" class="item-image">
            </a>
            <div class="item-name">{{ $item->name }}</div>
        </div>
        @endforeach
        @else
        @foreach ($purchasedItems as $item)
        <div class="item-card">
            <a href="{{ route('items.show', $item) }}">
                <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                    alt="{{ $item->name }}" class="item-image">
            </a>
            <div class="item-name">{{ $item->name }}</div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection