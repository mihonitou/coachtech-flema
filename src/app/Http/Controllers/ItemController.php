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
        $query = $request->query('query');

        if ($tab === 'mylist') {
            if (Auth::check()) {
                // ログインユーザーのいいね済み商品
                $items = auth()->user()->favorites()
                    ->when($query, function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->latest()->get();
            } else {
                // 未ログインユーザーのセッション内のいいねIDを取得
                $likedIds = session()->get('guest_likes', []);
                $items = Item::whereIn('id', $likedIds)
                    ->when($query, function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->latest()->get();
            }

            return view('items.mylist', compact('items'));
        }

        // 「おすすめ」一覧（全商品）
        $items = Item::when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })->latest()->get();

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
            : in_array($item->id, session('guest_likes', []));

        return view('items.show', compact(
            'item',
            'likeCount',
            'commentCount',
            'userLiked'
        ));
    }

    public function toggleLike(Item $item)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $existing = $item->likes()->where('user_id', $user->id);
            if ($existing->exists()) {
                $existing->delete();
                $status = 'removed';
            } else {
                $item->likes()->create(['user_id' => $user->id]);
                $status = 'added';
            }
        } else {
            // 未ログインユーザーはセッションで管理
            $likedItems = session()->get('guest_likes', []);

            if (in_array($item->id, $likedItems)) {
                $likedItems = array_diff($likedItems, [$item->id]);
                $status = 'removed';
            } else {
                $likedItems[] = $item->id;
                $status = 'added';
            }

            session(['guest_likes' => array_values($likedItems)]);
        }

        return response()->json([
            'status' => $status,
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
