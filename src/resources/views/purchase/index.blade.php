@extends('layouts.app')

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

<div class="purchase-container">

    <form id="purchase-form" method="POST" data-item-id="{{ $item->id }}">
        @csrf

        {{-- 左カラム --}}
        <div class="left-column">
            {{-- 商品情報 --}}
            <div class="item-info">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" width="100%">
                <h2>{{ $item->name }}</h2>
                <p>価格：¥{{ number_format($item->price) }}</p>
            </div>

            <div class="section-divider"></div>

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
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address.edit', ['item' => $item->id]) }}" class="change-address-link">変更する</a>
                </div>

                {{-- 配送先未登録メッセージ --}}
                @if (
                empty(old('postal_code')) && empty($shippingAddress?->postal_code) && empty($user->postal_code)
                || empty(old('address')) && empty($shippingAddress?->address) && empty($user->address)
                )
                <div class="no-shipping-warning">※ まだ配送先が登録されていません。</div>
                @endif

                {{-- 郵便番号 --}}
                <div class="address-block">
                    <label>郵便番号</label>
                    <span>
                        @if (old('postal_code'))
                        {{ old('postal_code') }}
                        @elseif (!empty($shippingAddress?->postal_code))
                        {{ $shippingAddress->postal_code }}
                        @else
                        {{ $user->postal_code }}
                        @endif
                    </span>
                    <input type="hidden" name="postal_code"
                        value="{{ old('postal_code') ?? $shippingAddress?->postal_code ?? $user->postal_code }}">
                </div>

                {{-- 住所 --}}
                <div class="address-block">
                    <label>住所</label>
                    <span>
                        @if (old('address'))
                        {{ old('address') }}
                        @elseif (!empty($shippingAddress?->address))
                        {{ $shippingAddress->address }}
                        @else
                        {{ $user->address }}
                        @endif
                    </span>
                    <input type="hidden" name="address"
                        value="{{ old('address') ?? $shippingAddress?->address ?? $user->address }}">
                </div>

                {{-- 建物名（任意）--}}
                <div class="address-block">
                    <label>建物名</label>
                    <span>
                        @if (old('building'))
                        {{ old('building') }}
                        @elseif (!empty($shippingAddress?->building))
                        {{ $shippingAddress->building }}
                        @else
                        {{ $user->building ?? '' }}
                        @endif
                    </span>
                    <input type="hidden" name="building"
                        value="{{ old('building') ?? $shippingAddress?->building ?? $user->building }}">
                </div>

                {{-- 送付先IDがあれば hidden で送信 --}}
                @if($shippingAddress)
                <input type="hidden" name="shipment_address_id" value="{{ $shippingAddress->id }}">
                @endif
            </div>

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
                    <td>
                        @if(old('payment_method'))
                        {{ old('payment_method') }}
                        @elseif(isset($shippingAddress) && $shippingAddress->payment_method)
                        {{ $shippingAddress->payment_method }}
                        @else
                        未選択
                        @endif
                    </td>
                </tr>
            </table>

            <button type="submit" class="purchase-button">購入する</button>
        </div>

    </form>

</div>

@endsection

@section('js')

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                option.textContent = '✔ ' + option.dataset.value;
                selected.textContent = option.textContent;

                // hidden input に選択値をセット
                hiddenInput.value = option.dataset.value;

                // フォームの action を変更
                if (option.dataset.value === 'カード払い') {
                    form.action = `/purchase/checkout/${itemId}`;
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

            // 念のため action 再設定（信頼性向上のため）
            if (!form.action.includes('/purchase')) {
                if (hiddenInput.value === 'カード払い') {
                    form.action = `/purchase/checkout/${itemId}`;
                } else {
                    form.action = `/purchase/${itemId}`;
                }
            }
        });
    });
</script>
@endsection