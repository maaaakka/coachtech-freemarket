<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;



Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');


// 📩 メール認証関連（ログインしてれば見れる）
Route::middleware('auth')->group(function () {

    Route::get('/email/verify', fn () => view('auth.verify-email'))
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('profile.edit'); // 認証後プロフィールへ
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back();
    })->middleware('throttle:6,1')->name('verification.send');

    // 👤 プロフィールは verified 不要！！
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


// ✅ 認証完了ユーザー専用エリア
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/like/{item}', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item}', [LikeController::class, 'destroy'])->name('like.destroy');
    Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comment.store');

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');

    Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/purchase/address/{item}', [AddressController::class, 'update'])->name('address.update');

    Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    Route::get('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    Route::get('/purchase/{item}/checkout', [PurchaseController::class, 'checkout'])->name('purchase.checkout');

    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

});

    Route::post('/stripe/webhook', [PurchaseController::class, 'webhook']);

    