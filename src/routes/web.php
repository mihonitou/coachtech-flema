<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // ← ここに追加
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\LoginController; // ← 追加
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');


// ✅ ログインルートをここに追加（Fortifyによるログイン処理を置き換える）
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [LoginController::class, 'store']);

// ✅ ログアウトルート（← ここを追加！）
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/'); // ← 商品一覧（ログイン前）へリダイレクト
})->name('logout');


// ✅ メール認証関連のルート（Fortify UI）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // email_verified_at を更新

    // ✅ セッションに just_registered がある → 新規登録直後の認証
    if (session()->pull('just_registered')) {
        return redirect()->route('mypage.edit'); // プロフィール編集画面へ
    }

    // ✅ 通常のログイン後の認証
    return redirect()->route('home'); // 商品一覧へ
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// ✅ ログイン ＋ メール認証が必要なルート
Route::middleware(['auth', 'verified'])->group(function () {

    // いいね・コメント・出品
    Route::post('/item/{item}/like', [ItemController::class, 'toggleLike'])->name('items.toggle_like');
    Route::post('/item/{item}/comment', [ItemController::class, 'addComment'])->name('items.comment');
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');

    // 購入関連
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::post('/purchase/{item}/checkout', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/{item}/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    // 表示用: 編集画面を開く
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    // 更新用: PUT（本来の正規ルート）
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    // フォーム互換のため、POSTでも同じアクションを許可する ← 追加
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update.fallback');
    // マイページ関連
    Route::get('/mypage', [UserController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('mypage.update');
});
