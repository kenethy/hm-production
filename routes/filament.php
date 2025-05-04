<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Custom admin login routes
Route::get('/admin/custom-login', [AdminLoginController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/admin/custom-login', [AdminLoginController::class, 'login'])
    ->name('admin.login.post');

Route::post('/admin/custom-logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout');

// Direct login route for Filament
Route::post('/admin/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect('/admin');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware([
    'web',
    'Illuminate\Cookie\Middleware\EncryptCookies',
    'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
    'Illuminate\Session\Middleware\StartSession',
    'Illuminate\View\Middleware\ShareErrorsFromSession',
    'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken',
    'Illuminate\Routing\Middleware\SubstituteBindings',
])->name('filament.admin.auth.login.post');
