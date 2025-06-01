<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');

        if ($tab === 'mylist') {
            if (!auth()->check()) {
                return redirect()->route('login');
            }

            $user = auth()->user();

            if (method_exists($user, 'favorites')) {

                $items = $user->favorites()->latest()->get();
            } else {
                $items = collect(); // favorites メソッドがなければ空コレクション
            }
            return view('items.mylist', compact('items'));
        }

        $items = Item::latest()->get(); // おすすめ
        return view('items.index', compact('items'));
    }


    public function show(Item $item)
    {
        // リレーションをロード（Eager Loading）
        $item->load(['categories', 'comments.user', 'likes']);

        // 合計いいね数・コメント数
        $likeCount    = $item->likes->count();
        $commentCount = $item->comments->count();

        // 今のユーザーがいいね済みか
        $userLiked = auth()->check()
            ? $item->likes->contains('user_id', auth()->id())
            : false;

        return view('items.show', compact(
            'item',
            'likeCount',
            'commentCount',
            'userLiked'
        ));
    }

    public function toggleLike(Item $item)
    {
        $user = Auth::user();

        // すでにいいね済みなら解除、そうでなければ追加
        $existing = $item->likes()->where('user_id', $user->id);
        if ($existing->exists()) {
            $existing->delete();
            $status = 'removed';
        } else {
            $item->likes()->create(['user_id' => $user->id]);
            $status = 'added';
        }

        // 最新のいいね数を返す
        return response()->json([
            'status'    => $status,
            'likeCount' => $item->likes()->count(),
        ]);
    }

    public function addComment(CommentRequest $request, Item $item)
    {
        $item->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->route('items.show', $item)->with('success', 'コメントを投稿しました。');
    }

    public function create()
    {
        return view('sell.create'); // 出品画面 Blade
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        // 画像アップロード
        $imagePath = $request->file('image')->store('item_images', 'public');

        // 商品登録
        $item = Item::create([
            'user_id' => auth()->id(),
            'image_path' => $imagePath, // ✅ テーブルのカラム名に一致
            'name' => $validated['name'],
            'brand_name' => $request->input('brand'), // ✅ brand_nameに合わせる
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['condition'], // ✅ カラム名は status
        ]);

        // カテゴリの関連付け
        $item->categories()->sync($validated['categories']);

        return redirect('/')->with('success', '商品を出品しました。');
    }
}
