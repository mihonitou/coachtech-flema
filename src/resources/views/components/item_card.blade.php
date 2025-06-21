@php use Illuminate\Support\Str; @endphp

<div class="item-card">
    <div class="item-image-container">
        <a href="{{ route('items.show', $item) }}">
            <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                alt="{{ $item->name }}" class="item-image">
        </a>



        @if (method_exists($item, 'isSold') && $item->isSold())
        <span class="sold-label"></span>
        @endif
    </div>
    <div class="item-name">{{ $item->name }}</div>
</div>