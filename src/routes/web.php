<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;


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

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])
    ->name('items.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/like/{item}', [LikeController::class, 'store'])
        ->name('like.store');

    Route::delete('/like/{item}', [LikeController::class, 'destroy'])
        ->name('like.destroy');

    Route::post('/comment/{item}', [CommentController::class, 'store'])
        ->name('comment.store');

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');

    Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])
    ->name('address.edit');

    Route::post('/purchase/address/{item}', [AddressController::class, 'update'])
    ->name('address.update');

    Route::get('/purchase/{item}', [PurchaseController::class, 'confirm'])
    ->name('purchase.confirm');

});