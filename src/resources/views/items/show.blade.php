@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_show.css') }}">
@endsection

@section('content')

<div class="item-detail-container">
    {{-- 商品画像 --}}
    <div class="item-image-box">
        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="item-image">
    </div>

    {{-- 商品情報 --}}
    <div class="item-info-box">
        <h1 class="item-name">{{ $item->name }}</h1>
        <div class="brand-name">{{ $item->brand_name }}</div>
        <div class="item-price">¥{{ number_format($item->price) }} <span class="tax">（税込）</span></div>

        {{-- いいね・コメントアイコン --}}
        <div class="item-stats">
            <button class="like-button" data-item-id="{{ $item->id }}">
                <i class="{{ $userLiked ? 'fas' : 'far' }} fa-star"></i>
                <span class="like-count">{{ $likeCount }}</span>
            </button>
            <span class="comment-icon">💬 {{ $commentCount }}</span>
        </div>

        <a href="{{ route('purchase.show', $item->id) }}" class="purchase-btn">購入手続きへ</a>

        {{-- 商品説明 --}}
        <h2 class="section-title">商品説明</h2>
        <div class="description">
            @if (!empty($item->color))
            <p><strong>カラー：</strong>{{ $item->color }}</p>
            @endif
            <p>{!! nl2br(e($item->description)) !!}</p>
        </div>

        {{-- 商品の情報 --}}
        <h2 class="section-title">商品の情報</h2>
        <div class="item-details">
            <div><strong>カテゴリー：</strong>
                @foreach($item->categories as $category)
                <span class="category-badge">{{ $category->name }}</span>
                @endforeach
            </div>
            <div><strong>商品の状態：</strong>{{ $item->status }}</div>
        </div>
    </div>
</div>

{{-- コメント表示 --}}
<div class="comments-section">
    <h2 class="section-title">コメント ({{ $item->comments->count() }})</h2>

    @foreach($item->comments as $comment)
    <div class="comment-box">
        <div class="user-icon">👤</div>
        <div class="comment-content">
            <strong>{{ $comment->user->name }}</strong>
            <p>{{ $comment->comment }}</p>
        </div>
    </div>
    @endforeach

    {{-- コメントフォーム --}}
    @if (Auth::check())
    <form method="POST" action="{{ route('items.comment', $item->id) }}" class="comment-form">
        @csrf
        <label for="comment">商品へのコメント</label>
        <textarea name="comment" id="comment" rows="4" required>{{ old('comment') }}</textarea>

        @error('comment')
        <p class="error">{{ $message }}</p>
        @enderror

        <button type="submit" class="comment-submit-btn">コメントを送信する</button>
    </form>
    @else
    <p class="login-reminder">コメントするにはログインしてください。</p>
    @endif
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.like-button').forEach(function(button) {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const icon = this.querySelector('i');
                const likeCountSpan = this.querySelector('.like-count');

                fetch(`/items/${itemId}/toggle-like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        // アイコン切り替え
                        if (data.status === 'added') {
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }

                        // いいね数更新
                        if (likeCountSpan) {
                            likeCountSpan.textContent = data.likeCount;
                        }
                    })
                    .catch(error => {
                        console.error('エラー:', error);
                    });
            });
        });
    });
</script>

@endsection