<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\ShipmentAddress; // ← 修正ポイント
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\URL;

class PurchaseController extends Controller
{
    // 購入画面表示
    public function show(Item $item)
    {
        $user = Auth::user();

        // 購入レコードから住所取得（未購入なら null）
        $purchase = Purchase::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->with('shipmentAddress')
            ->first();

        // PHP 7.x 対応の書き方
        $shippingAddress = $purchase ? $purchase->shipmentAddress : null;

        return view('purchase.index', compact('item', 'user', 'shippingAddress'));
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

    public function checkout(PurchaseRequest $request, Item $item)
    {
        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        $user = Auth::user();

        // Stripe APIキーの設定
        Stripe::setApiKey(config('services.stripe.secret')); // config/services.phpに設定しておくと安全

        // 商品情報
        $lineItems = [[
            'price_data' => [
                'currency' => 'jpy',
                'product_data' => [
                    'name' => $item->name,
                    'description' => $item->description,
                ],
                'unit_amount' => $item->price * 100, // 円→最小単位（例：100円 → 10000）
            ],
            'quantity' => 1,
        ]];

        // 成功・キャンセル後のリダイレクトURL
        $successUrl = URL::route('purchase.success', ['item' => $item->id]);
        $cancelUrl = URL::route('purchase.cancel', ['item' => $item->id]);

        // セッション作成
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'customer_email' => $user->email,
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
        ]);

        // 必要な情報をセッションやDBに一時保存（後ほど使用）
        session([
            'purchase_postal_code' => $request->postal_code,
            'purchase_address' => $request->address,
            'purchase_building' => $request->building,
            'purchase_payment_method' => 'card',
        ]);

        return redirect($session->url);
    }

    public function success(Item $item)
    {
        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        $user = Auth::user();

        // 商品状態を更新
        $item->update(['is_sold' => true]);

        // 購入記録を作成
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 住所登録（セッションから取り出す）
        ShipmentAddress::updateOrCreate(
            ['item_id' => $item->id],
            [
                'user_id' => $user->id,
                'postal_code' => session('purchase_postal_code'),
                'address' => session('purchase_address'),
                'building' => session('purchase_building'),
                'payment_method' => session('purchase_payment_method'),
            ]
        );

        return redirect()->route('items.index')->with('success', '決済が完了しました。');
    }

    public function cancel(Item $item)
    {
        return redirect()->route('purchase.show', ['item' => $item->id])->with('error', '決済がキャンセルされました。');
    }

    public function editAddress(Item $item)
    {
        // ログインユーザーがこの商品に設定していた住所（購入前でもOK）を取得
        $shippingAddress = ShipmentAddress::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->first();


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

        $data = $request->only(['postal_code', 'address', 'building']);

        // 住所が既にあるか確認
        if ($purchase->shipmentAddress) {
            // 更新
            $purchase->shipmentAddress->update($data);
        } else {
            // 新規作成
            $purchase->shipmentAddress()->create($data);
        }

        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('success', '住所を更新しました');
    }
}
