@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_index.css') }}">
@endsection

@section('content')

@if (session('success'))
<div class="alert__message">{{ session('success') }}</div>
@endif

@if (session('error'))
<div class="alert__danger">{{ session('error') }}</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="purchase-container">


    {{-- 左カラム --}}
    <div class="left-column">
        {{-- 商品情報 --}}
        <div class="item-info">
            <img src="{{ Str::startsWith($item->image_path, ['http://', 'https://']) ? $item->image_path : asset('storage/' . $item->image_path) }}"
                alt="{{ $item->name }}"
                width="100%">
            <h2>{{ $item->name }}</h2>
            <p>価格：¥{{ number_format($item->price) }}</p>
        </div>

        <div class="section-divider"></div>

        <form id="purchase-form" method="POST" action="{{ route('purchase.store', ['item' => $item->id]) }}" data-item-id="{{ $item->id }}">
            @csrf

            <div class="custom-select-wrapper">
                <h3>支払い方法</h3>
                <div class="custom-select" id="customSelect">
                    <div class="selected">選択してください</div>
                    <ul class="select-options">
                        <li data-value="コンビニ払い">✔ コンビニ払い</li>
                        <li data-value="カード払い">✔ カード払い</li>
                    </ul>
                </div>
                <input type="hidden" name="payment_method" id="paymentInput" required>

                {{-- エラーメッセージ --}}
                @error('payment_method')
                <div class="error-message">{{ $message }}</div>
                @enderror

            </div>

            <div class="section-divider"></div>

            {{-- 配送先フォーム --}}
            <div class="shipping-address">
                <div class="shipping-header">
                    <p>確認用: 商品ID = {{ $item->id }}</p>
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address.edit', ['item' => $item->id]) }}" class="change-address-link">変更する</a>
                </div>

                {{-- 配送先未登録メッセージ --}}
                @php
                $noPostal = empty(old('postal_code')) && empty(optional($shippingAddress)->postal_code) && empty($user->postal_code);
                $noAddress = empty(old('address')) && empty(optional($shippingAddress)->address) && empty($user->address);
                @endphp



                {{-- 郵便番号 --}}
                <div class="address-block">
                    <label>郵便番号</label>
                    <span>
                        @if (old('postal_code'))
                        {{ old('postal_code') }}
                        @elseif (!empty(optional($shippingAddress)->postal_code))
                        {{ optional($shippingAddress)->postal_code }}
                        @else
                        {{ $user->postal_code }}
                        @endif
                    </span>
                    <input type="hidden" name="postal_code"
                        value="{{ old('postal_code') ?? optional($shippingAddress)->postal_code ?? $user->postal_code }}">
                </div>

                {{-- 住所 --}}
                <div class="address-block">
                    <label>住所</label>
                    <span>
                        @if (old('address'))
                        {{ old('address') }}
                        @elseif (!empty(optional($shippingAddress)->address))
                        {{ optional($shippingAddress)->address }}
                        @else
                        {{ $user->address }}
                        @endif
                    </span>
                    <input type="hidden" name="address"
                        value="{{ old('address') ?? optional($shippingAddress)->address ?? $user->address }}">
                </div>

                {{-- 建物名（任意）--}}
                <div class="address-block">
                    <label>建物名</label>
                    <span>
                        @if (old('building'))
                        {{ old('building') }}
                        @elseif (!empty(optional($shippingAddress)->building))
                        {{ optional($shippingAddress)->building }}
                        @else
                        {{ $user->building ?? '' }}
                        @endif
                    </span>
                    <input type="hidden" name="building"
                        value="{{ old('building') ?? optional($shippingAddress)->building ?? $user->building }}">
                </div>

                {{-- 送付先IDがあれば hidden で送信 --}}
                @if($shippingAddress)
                <input type="hidden" name="shipment_address_id" value="{{ $shippingAddress->id }}">
                @endif
            </div>

        </form> {{-- ← formはここで閉じる --}}
    </div>

    {{-- 右カラム --}}
    <div class="purchase-summary">
        <table class="summary-table">
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td id="payment-method-display">
                    @if(old('payment_method'))
                    {{ old('payment_method') }}
                    @elseif(isset($shippingAddress) && optional($shippingAddress)->payment_method)
                    {{ optional($shippingAddress)->payment_method }}
                    @else
                    未選択
                    @endif
                </td>
            </tr>
        </table>

        {{-- formの外だが、form属性で紐付けて送信可能に --}}
        <button type="submit" form="purchase-form" class="purchase-button" id="purchase-button">購入する</button>

    </div>



</div>

@endsection

@section('js')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('JSは読み込まれました'); // ← これを追加
        const selectWrapper = document.getElementById('customSelect');
        const selected = selectWrapper.querySelector('.selected');
        const optionsList = selectWrapper.querySelector('.select-options');
        const options = optionsList.querySelectorAll('li');
        const hiddenInput = document.getElementById('paymentInput');
        const form = document.getElementById('purchase-form');
        const itemId = form.dataset.itemId; // ← ここで取得


        // プルダウン開閉
        selected.addEventListener('click', function() {
            optionsList.style.display = optionsList.style.display === 'block' ? 'none' : 'block';
        });

        // 選択肢クリック時の処理
        options.forEach(option => {
            option.addEventListener('click', function() {
                // すべてリセット
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.textContent = opt.textContent.replace('✔ ', '');
                });

                // チェックとスタイル追加
                option.classList.add('active');
                option.textContent = option.dataset.value; // ✔ を付けないで戻す
                selected.textContent = '✔ ' + option.dataset.value; // ✔ は選択中表示だけに

                // hidden input に選択値をセット
                hiddenInput.value = option.dataset.value;

                // 支払い方法表示欄にもリアルタイム反映 ←★この行を追加★
                const methodDisplay = document.getElementById('payment-method-display');
                if (methodDisplay) {
                    methodDisplay.textContent = option.dataset.value;
                }

                // フォームの action を変更
                if (option.dataset.value === 'カード払い') {
                    form.action = `/purchase/${itemId}/checkout`; // ← 正しい構文
                } else {
                    form.action = `/purchase/${itemId}`;
                }

                // プルダウンを閉じる
                optionsList.style.display = 'none';
            });
        });

        // ページ外クリックでプルダウンを閉じる
        document.addEventListener('click', function(e) {
            if (!selectWrapper.contains(e.target)) {
                optionsList.style.display = 'none';
            }
        });

        // フォーム送信前にチェック（選択されていなければキャンセル）
        form.addEventListener('submit', function(e) {
            if (!hiddenInput.value) {
                e.preventDefault();
                alert('支払い方法を選択してください');
                return;
            }

            // ✅ form.action を常に上書き（信頼性向上）
            if (hiddenInput.value === 'カード払い') {
                form.action = `/purchase/${itemId}/checkout`;
            } else {
                form.action = `/purchase/${itemId}`;
            }
        });

        // ←ここに追加してOK
        const purchaseButton = document.getElementById('purchase-button');
        if (purchaseButton) {
            purchaseButton.addEventListener('click', function() {
                console.log('購入ボタンがクリックされました');
            });
        }
    });
</script>
@endsection