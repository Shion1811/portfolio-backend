<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UrlsController;

// ログインページ（authミドルウェアがリダイレクトする先）
Route::get('/', function () {
    return view('welcome');
})->name('login');

// postでリクエストをし、AdminControllerのadminメソッドを実行し、nameをadminとする
// Route::post('/aaa', [AdminController::class, 'admin'])->name('admin');
Route::post('/login', [LoginController::class, 'login'])->name('login-post');

// 管理画面ルートを認証ミドルウェアで保護
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'adminCreate'])->name('admin-get');
    Route::post('/admin',[AdminController::class, 'adminCreate'])->name('admin-post');
    Route::get('/admin-list',[AdminController::class, 'adminList'])->name('admin-list-get');
    Route::post('/admin-list', [AdminController::class, 'adminListPost'])->name('admin-list-post');
    Route::post('/admin-list-delete', [AdminController::class, 'adminListDelete'])->name('admin-list-delete');
    Route::post('/admin-select', [AdminController::class, 'adminSelect'])->name('admin-select');
    // URL管理（管理画面でも利用可能）
    Route::get('/urls', [UrlsController::class, 'index'])->name('urls.index');
    Route::post('/urls', [UrlsController::class, 'store'])->name('urls.store');
});