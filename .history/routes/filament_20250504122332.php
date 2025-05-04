<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;

// Custom admin login routes
Route::get('/admin/custom-login', [AdminLoginController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/admin/custom-login', [AdminLoginController::class, 'login'])
    ->name('admin.login.post');

Route::post('/admin/custom-logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout');
