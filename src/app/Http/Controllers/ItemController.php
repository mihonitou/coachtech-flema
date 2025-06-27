<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;

use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
        }

        $tab = $request->query('tab');
        $query = $request->query('query');

        if ($tab === 'mylist') {
            if (Auth::check()) {
                // ログインユーザーの「いいね」かつ自分の出品以外
                $items = auth()->user()->favorites()
                    ->where('items.user_id', '<>', auth()->id())
                    ->when($query, function ($q) use ($query) {
                        $q->where('items.name', 'like', "%{$query}%");
                    })
                    ->latest('items.created_at')
                    ->get();
            } else {
                // 未ログインユーザー：セッションに保存されたIDから
                $likedIds = session()->get('guest_likes', []);
                $items = Item::whereIn('id', $likedIds)
                    ->when($query, function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->latest()->get();
            }

            return view('items.mylist', compact('items'));
        }


        // おすすめタブ（自分の出品を除外）
        $items = Item::when(auth()->check(), function ($q) {
            $q->where('user_id', '<>', auth()->id());
        })
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->latest()
            ->get();

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
        Log::info('toggleLike start. item_id: ' . $item->id . ', user_id: ' . Auth::id());


        // 未ログインなら401 Unauthorizedを返す（保険）
        if (!Auth::check()) {
            return response()->json([
                'message' => 'ログインが必要です。',
            ], 401);
        }

        $user = Auth::user();

        // 既にいいね済みか確認
        $existing = $item->likes()->where('user_id', $user->id);

        if ($existing->exists()) {
            // いいねを解除
            $existing->delete();
            $status = 'removed';
        } else {
            // いいねを追加
            $item->likes()->create([
                'user_id' => $user->id,
            ]);
            $status = 'added';
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
        return view('items.create'); // 出品画面 Blade
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
