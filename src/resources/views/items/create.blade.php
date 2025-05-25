@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')
<div class="sell-wrapper">
    <h2 class="sell-title">商品の出品</h2>

    {{-- 成功メッセージ --}}
    @if(session('success'))
    <div class="alert-message">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-block">
            <label>商品画像</label>
            <div class="image-upload-box">
                <input type="file" name="image" id="image" class="hidden-input">
                <label for="image" class="image-label">画像を選択する</label>
            </div>
            @error('image')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <hr class="divider">

        {{-- 商品の詳細 --}}
        <div class="form-block">
            <p class="section-title">商品の詳細</p>
        </div>
        <hr class="divider">

        {{-- カテゴリー --}}
        <div class="form-block">
            <label>カテゴリー</label>
            <div class="category-tags">
                @foreach(['ファッション', '家電', 'インテリア', 'レディース', 'メンズ', 'コスメ', '本', 'ゲーム', 'スポーツ', 'キッチン', 'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ'] as $key => $category)
                <label class="tag">
                    <input type="checkbox" name="categories[]" value="{{ $key + 1 }}">
                    {{ $category }}
                </label>
                @endforeach
            </div>
            @error('categories')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 商品の状態 --}}
        <div class="form-block">
            <label for="condition">商品の状態</label>
            <select name="condition" id="condition" class="form-select">
                <option disabled selected>選択してください</option>
                <option value="新品">新品</option>
                <option value="未使用に近い">未使用に近い</option>
                <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option value="傷や汚れあり">傷や汚れあり</option>
            </select>
            @error('condition')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 商品名と説明 --}}
        <div class="form-block">
            <p class="section-title">商品名と説明</p>
        </div>
        <hr class="divider">

        {{-- 商品名 --}}
        <div class="form-block">
            <label for="name">商品名</label>
            <input type="text" name="name" id="name" class="form-input">
            @error('name')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- ブランド名 --}}
        <div class="form-block">
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="brand" class="form-input">
        </div>

        {{-- 商品の説明 --}}
        <div class="form-block">
            <label for="description">商品の説明</label>
            <textarea name="description" id="description" class="form-textarea"></textarea>
            @error('description')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 販売価格 --}}
        <div class="form-block">
            <label for="price">販売価格</label>
            <div class="price-input-wrapper">
                <span class="yen-symbol">¥</span>
                <input type="number" name="price" id="price" class="form-input">
            </div>
            @error('price')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 出品ボタン --}}
        <div class="form-block">
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection