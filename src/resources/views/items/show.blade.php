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
            <span
                id="like-button"
                class="like-icon {{ $userLiked ? 'liked' : '' }}"
                data-liked="{{ $userLiked ? '1' : '0' }}"
                data-item-id="{{ $item->id }}">⭐ <span id="like-count">{{ $likeCount }}</span>
            </span>
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
        const likeButton = document.getElementById('like-button');
        const likeCountEl = document.getElementById('like-count');

        likeButton.addEventListener('click', async function() {
            const itemId = this.dataset.itemId;
            const liked = this.dataset.liked === '1';

            try {
                const res = await fetch(`/items/${itemId}/toggle-like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                });

                if (!res.ok) throw new Error('Network response failed');

                const data = await res.json();

                likeCountEl.textContent = data.likeCount;
                this.dataset.liked = data.status === 'added' ? '1' : '0';

                // 見た目切り替え
                this.classList.toggle('liked', data.status === 'added');
            } catch (err) {
                console.error('Error toggling like:', err);
            }
        });
    });
</script>
@endsection