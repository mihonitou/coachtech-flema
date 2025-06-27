@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mylist.css') }}">
@endsection

@section('content')

@if (session('success'))
<div class="alert-message success">
    {{ session('success') }}
</div>
@endif

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
    <p class="no-items">
        @if (!empty(request('query')))
        該当する商品が見つかりませんでした。
        @else
        現在、いいねした商品はありません。
        @endif
    </p>
    @else
    <div class="item-grid">
        @foreach ($items as $item)
        @include('components.item_card', ['item' => $item])
        @endforeach
    </div>
    @endif

</div>
@endsection