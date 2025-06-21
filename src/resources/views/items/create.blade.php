@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')
<div class="sell-wrapper">
    <h2 class="sell-title">商品の出品</h2>

    {{-- 成功メッセージ --}}
    @if(session('success'))
    <div class="alert__message">
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
                @foreach([
                'ファッション', '家電', 'インテリア', 'レディース', 'メンズ',
                'コスメ', '本', 'ゲーム', 'スポーツ', 'キッチン',
                'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ', '食品', 'その他'
                ] as $key => $category)
                <label class="tag">
                    <input type="checkbox" name="categories[]" value="{{ $key + 1 }}"
                        {{ is_array(old('categories')) && in_array($key + 1, old('categories')) ? 'checked' : '' }}>
                    <span>{{ $category }}</span>
                </label>
                @endforeach
            </div>
            @foreach ($errors->get('categories.*') as $messages)
            @foreach ($messages as $msg)
            <div class="error-message">{{ $msg }}</div>
            @endforeach
            @endforeach

        </div>

        {{-- 商品の状態 --}}
        <div class="form-block">
            <label for="status">商品の状態</label>
            <div class="custom-select-wrapper">
                <div class="custom-select-trigger">
                    {{ old('status', '選択してください') }}
                </div>
                <div class="custom-options">
                    @foreach(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'] as $value)
                    <span class="custom-option {{ old('status') === $value ? 'selected' : '' }}"
                        data-value="{{ $value }}">{{ $value }}</span>
                    @endforeach
                </div>
                <input type="hidden" name="status" id="status" value="{{ old('status') }}">
            </div>
            @error('status')
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
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}">
            @error('name')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- ブランド名 --}}
        <div class="form-block">
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="brand" class="form-input" value="{{ old('brand') }}">
        </div>

        {{-- 商品の説明 --}}
        <div class="form-block">
            <label for="description">商品の説明</label>
            <textarea name="description" id="description" class="form-textarea">{{ old('description') }}</textarea>
            @error('description')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 販売価格 --}}
        <div class="form-block">
            <label for="price">販売価格</label>
            <div class="price-input-wrapper">
                <span class="yen-symbol">¥</span>
                <input type="number" name="price" id="price" class="form-input" value="{{ old('price') }}">
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

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const trigger = document.querySelector('.custom-select-trigger');
        const options = document.querySelector('.custom-options');
        const optionElements = document.querySelectorAll('.custom-option');
        const hiddenInput = document.getElementById('status');

        // 初期選択状態を反映
        const selectedOption = document.querySelector('.custom-option.selected');
        if (selectedOption) {
            trigger.textContent = selectedOption.textContent;
            hiddenInput.value = selectedOption.dataset.value;
        }

        // トグル表示
        trigger.addEventListener('click', (e) => {
            e.stopPropagation(); // 内部クリックが外部クリック判定されないように
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        // オプション選択処理
        optionElements.forEach(option => {
            option.addEventListener('click', (e) => {
                e.stopPropagation();
                trigger.textContent = option.textContent;
                hiddenInput.value = option.dataset.value;

                optionElements.forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');

                options.style.display = 'none';
            });
        });

        // 外部クリックで閉じる
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-select-wrapper')) {
                options.style.display = 'none';
            }
        });
    });
</script>
@endsection