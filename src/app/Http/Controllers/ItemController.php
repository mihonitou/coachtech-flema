<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;


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
}
