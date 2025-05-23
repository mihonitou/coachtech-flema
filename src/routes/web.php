<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UseController;
use App\Http\Controllers\PurchaseController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/items', [ItemController::class, 'mylist'])->name('items.mylist');
    Route::post('/item/{item}/like', [ItemController::class, 'toggleLike'])->name('items.toggle_like');
    Route::post('/item/{item}/comment', [ItemController::class, 'addComment'])->name('items.comment');
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 表示用: 編集画面を開く
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');

    // 更新用: 送信された住所を保存
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');



    Route::get('/mypage/profile', [UseController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [UseController::class, 'update'])->name('profile.update');
});
