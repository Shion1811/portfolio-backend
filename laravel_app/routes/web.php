<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});

// postでリクエストをし、AdminControllerのadminメソッドを実行し、nameをadminとする
// Route::post('/aaa', [AdminController::class, 'admin'])->name('admin');
Route::post('/login', [LoginController::class, 'login'])->name('login-post');
// Route::post('/admin', [AdminController::class, 'adminCreate'])->name('admin-post');
Route::get('/admin', [AdminController::class, 'adminCreate'])->name('admin-get');
Route::post('/admin',[AdminController::class, 'adminCreate'])->name('admin-post');
Route::get('/admin-list',[AdminController::class, 'adminList'])->name('admin-list-get');
Route::post('/admin-list', [AdminController::class, 'adminListPost'])->name('admin-list-post');
Route::post('/admin-list-delete', [AdminController::class, 'adminListDelete'])->name('admin-list-delete');
Route::post('/admin-select', [AdminController::class, 'adminSelect'])->name('admin-select');