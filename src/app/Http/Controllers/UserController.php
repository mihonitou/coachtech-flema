<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\User;


class UserController extends Controller
{
    // プロフィール画面の表示
    public function show(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('page', 'sell');

        // 出品商品
        $listedItems = $user->items()->latest()->get();

        // 購入商品（中間テーブル purchases を想定）
        $purchasedItems = $user->purchases()->latest()->get();

        return view('user.show', compact('user', 'tab', 'listedItems', 'purchasedItems'));
    }

    // プロフィール編集画面の表示
    public function edit()
    {
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 画像アップロード処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->fill($request->only(['name', 'postal_code', 'address', 'building']))->save();


        return redirect()->route('mypage')->with('success', 'プロフィールを更新しました。');
    }
}
