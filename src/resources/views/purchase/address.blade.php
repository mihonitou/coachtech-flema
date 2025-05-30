@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">住所の変更</h2>

    <form action="{{ route('purchase.address.update', ['item' => $item->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $shippingAddress->postal_code) }}">
            @error('postcode')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $shippingAddress->address) }}">
            @error('address')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="building" class="form-label">建物名</label>
            <input type="text" name="building" id="building" class="form-control" value="{{ old('building', $shippingAddress->building) }}">
            @error('building')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger">更新する</button>
    </form>
</div>
@endsection