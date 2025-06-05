@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mylist.css') }}">
@endsection

@section('content')

<div class="mylist-container">

    {{-- タブメニュー --}}
    <div class="tab-menu">
        <a href="{{ route('home') }}" class="tab {{ request('tab') !== 'mylist' ? 'tab-active' : 'tab-inactive' }}">
            おすすめ
        </a>
        <a href="{{ route('home', ['tab' => 'mylist', 'query' => request('query')]) }}" class="tab {{ request('tab') === 'mylist' ? 'tab-active' : 'tab-inactive' }}">
            マイリスト
        </a>
    </div>
    <hr class="tab-underline">

    {{-- 商品一覧 --}}
    @if ($items->isEmpty())
    <p class="no-items">現在、いいねした商品はありません。</p>
    @else
    <div class="item-grid">
        @foreach ($items as $item)
        <div class="item-card">
            <div class="item-image-container">
                <a href="{{ route('items.show', $item) }}">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="item-image">
                </a>
                @if ($item->isSold())
                <span class="sold-label"></span>
                @endif
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection