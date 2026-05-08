<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\LikeController;

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

Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// いいね
Route::post('/items/{item}/like', [LikeController::class, 'toggle'])->name('like.toggle')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::post('/mypage/profile', [ProfileController::class, 'update']);
});

// コメント
Route::post('/items/{item}/comment', [LikeController::class, 'store'])->name('comment.store')->middleware('auth');


Route::middleware('auth')->group(function () {

    // マイページ
    Route::get('/mypage', [MypageController::class, 'index']);

    // プロフィール
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 出品
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/items/sell', [ItemController::class, 'store']);

    // 購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item}/address', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/{item}/address', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});
