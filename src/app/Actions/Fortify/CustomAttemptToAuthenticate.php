<?php

namespace App\Actions\Fortify;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomAttemptToAuthenticate
{
    public function __invoke(LoginRequest $request)
    {
        // ✅ 明示的にバリデーションを実行
        $request->validate(
            $request->rules(),
            $request->messages(),
            $request->attributes()
        );

        // Fortifyの guard を取得
        $guard = config('fortify.guard');

        // 認証試行
        if (! Auth::guard($guard)->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません。'],
            ]);
        }

        // セッション再生成
        $request->session()->regenerate();

        // ログインユーザー取得
        $user = Auth::guard($guard)->user();

        // プロフィール未設定ならマイページ編集へリダイレクト
        if (! $user->postal_code || ! $user->address) {
            return redirect()->route('mypage.edit');
        }

        // 通常は intended('/') にリダイレクトされる
        return null;
    }
}
