<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\ShipmentAddress; // ← 修正ポイント
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    // 購入画面表示
    public function show(Item $item)
    {
        $user = Auth::user();

        // 住所：セッション or DB or プロフィール
        $shippingAddress = ShipmentAddress::where('item_id', $item->id)->first();

        return view('purchase.index', [
            'item' => $item,
            'user' => $user,
            'shippingAddress' => $shippingAddress,
        ]);
    }

    // 購入処理
    public function store(PurchaseRequest $request, Item $item)
    {
        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        $user = Auth::user();

        $item->is_sold = true;
        $item->save();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        ShipmentAddress::updateOrCreate(
            ['item_id' => $item->id],
            [
                'user_id' => $user->id,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
                'payment_method' => $request->payment_method,
            ]
        );

        return redirect()->route('items.index')->with('success', '商品を購入しました。');
    }

    public function editAddress(Item $item)
    {
        $purchase = Purchase::where('item_id', $item->id)
            ->where('user_id', Auth::id())
            ->firstOrFail(); // ← 購入履歴がなければ404

        $shippingAddress = $purchase->shipmentAddress;

        return view('purchase.address', [
            'item' => $item,
            'shippingAddress' => $shippingAddress,
        ]);
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {
        $purchase = Purchase::where('item_id', $item->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $shippingAddress = $purchase->shipmentAddress;

        $shippingAddress->update($request->only(['postal_code', 'address', 'building']));

        return redirect()->route('purchases.index')->with('success', '住所を更新しました');
    }
}
