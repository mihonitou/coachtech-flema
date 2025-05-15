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

        return view('purchase_index', [
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

    // 配送先変更画面の表示
    public function editAddress(Item $item)
    {
        $user = Auth::user();
        $shippingAddress = ShipmentAddress::where('item_id', $item->id)->first();

        return view('purchase_address_edit', [
            'item' => $item,
            'user' => $user,
            'shippingAddress' => $shippingAddress,
        ]);
    }

    // 送付先住所を保存
    public function updateAddress(AddressRequest $request, Item $item)
    {
        $user = Auth::user();

        ShipmentAddress::updateOrCreate(
            ['item_id' => $item->id],
            [
                'user_id' => $user->id,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect()->route('purchase.show', ['item' => $item->id])
            ->with('success', '配送先住所を更新しました。');
    }
}
