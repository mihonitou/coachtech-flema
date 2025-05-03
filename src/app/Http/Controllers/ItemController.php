<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            $items = Item::where('user_id', '!=', Auth::id())->get();
        } else {
            $items = Item::all();
        }

        return view('items.index', compact('items'));
    }

    public function mylist(Request $request)
    {
        if (!Auth::check()) {
            // 未ログインなら空の一覧を返す
            $items = collect();
        } else {
            $user = Auth::user();

            $tab = $request->query('tab'); // クエリパラメータを取得

            if ($tab === 'mylist') {
                // 「いいね」した商品を取得
                $items = $user->likes->pluck('item'); // likeリレーションからitemを集める
            } else {
                // 他のタブ（もし今後増やすなら）もここで分岐できる
                $items = collect();
            }
        }

        return view('items.mylist', compact('items'));
    }
}
