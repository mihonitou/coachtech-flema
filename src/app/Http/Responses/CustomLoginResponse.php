<?php

// app/Http/Responses/CustomLoginResponse.php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Http\RedirectResponse;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();

        // 初回ログイン時（プロフィール未設定）
        if (!$user->profile_completed) {
            return redirect('/mypage/profile');
        }

        // 通常ログイン時
        return redirect('/');
    }
}
