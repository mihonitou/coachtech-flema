<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        // バリデーション済のデータを取得
        $credentials = $request->only('email', 'password');

        // 認証処理（rememberチェック対応）
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません。'],
            ]);
        }

        // セッション再生成（セキュリティ対策）
        $request->session()->regenerate();

        $user = Auth::user(); // IDEには mixed 型と認識されることがある

        // 明示的に User 型として扱う（補助用）
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 認証成功 → 商品一覧へリダイレクト
        return redirect()->route('home');
    }
}
