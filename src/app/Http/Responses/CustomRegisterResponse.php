<?php

// app/Http/Responses/CustomRegisterResponse.php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Http\RedirectResponse;

class CustomRegisterResponse implements RegisterResponse
{
    public function toResponse($request): RedirectResponse
    {
        // 会員登録完了後にプロフィール設定画面へ
        return redirect('/mypage/profile');
    }
}
