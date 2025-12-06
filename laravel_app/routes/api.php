<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UrlsController;

// ========== API エンドポイント ==========

// ログイン
Route::post('/login', [LoginController::class, 'apiLogin'])->name('api.login');


// ログアウト
Route::post('/logout', [LoginController::class, 'apiLogout'])->name('api.logout');

// 認証状態確認
Route::get('/user', [LoginController::class, 'apiUser'])->name('api.user');

// ポートフォリオ一覧取得
Route::get('/portfolios', [AdminController::class, 'apiList'])->name('api.portfolios');

// ポートフォリオ作成
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/portfolios', [AdminController::class, 'apiCreate'])->name('api.portfolios.create');
    // URLの作成
    Route::post('/urls', [UrlsController::class, 'store'])->name('api.urls.store');
});

// ポートフォリオ削除
Route::delete('/portfolios/{id}', [AdminController::class, 'apiDelete'])->name('api.portfolios.delete');

// 利用可能なタグ一覧取得
Route::get('/tags', [AdminController::class, 'apiTags'])->name('api.tags');

// URL一覧取得（公開）
Route::get('/urls', [UrlsController::class, 'index'])->name('api.urls.index');
