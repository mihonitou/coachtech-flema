@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css') }}">
@endsection

@section('content')

<div class="index-container">

    {{-- タブメニュー --}}
    <div class="tab-menu">
        <a href="{{ route('home') }}" class="tab {{ request('tab') !== 'mylist' ? 'tab-active' : 'tab-inactive' }}">
            おすすめ
        </a>
        <a href="{{ route('home', ['tab' => 'mylist']) }}" class="tab {{ request('tab') === 'mylist' ? 'tab-active' : 'tab-inactive' }}">
            マイリスト
        </a>
    </div>
    <hr class="tab-underline">

    @if (!empty($query))
    <p class="search-result-label">「{{ $query }}」の検索結果</p>
    @endif

    @if ($items->isEmpty())
    <p class="no-items">
        @if (!empty($query))
        該当する商品が見つかりませんでした。
        @else
        現在、商品はありません。
        @endif
    </p>

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