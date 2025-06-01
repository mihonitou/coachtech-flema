<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class CustomAttemptToAuthenticate
{
    public function handle(Request $request)
    {
        // ① バリデーションを実行（LoginRequest）
        app(LoginRequest::class);

        // ② Fortifyの guard を取得
        $guard = config('fortify.guard');

        // ③ 認証試行
        if (! Auth::guard($guard)->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません。'],
            ]);
        }

        // ④ セッション再生成（Laravel公式推奨）
        $request->session()->regenerate();

        // ⑤ ログインユーザーを取得
        $user = Auth::guard($guard)->user();

        // ✅ プロフィール未設定ならマイページ編集に遷移
        if (! $user->postal_code || ! $user->address) {
            return redirect()->route('mypage.edit');
        }

        // 🔽 Fortifyがintended('/') にリダイレクトするため null を返す
        return null;
    }
}
