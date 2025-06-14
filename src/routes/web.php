<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
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
Route::post('/item/{item}/like', [ItemController::class, 'toggleLike'])->name('items.toggle_like');

// ✅ メール認証関連のルート（Fortify UI）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // email_verified_at を更新
    return redirect('/'); // 認証後のリダイレクト先（お好みで）
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// ✅ ログイン ＋ メール認証が必要なルート
Route::middleware(['auth', 'verified'])->group(function () {

    // いいね・コメント・出品

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
    // 更新用: 送信された住所を保存
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // マイページ関連
    Route::get('/mypage', [UserController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('mypage.update');
});
