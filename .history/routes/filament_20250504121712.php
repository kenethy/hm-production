<?php

use Illuminate\Support\Facades\Route;
use Filament\Pages\Auth\Login;

// Add the missing POST route for Filament login
Route::post('/admin/login', [Login::class, 'authenticate'])
    ->middleware([
        'panel:admin',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Filament\Http\Middleware\AuthenticateSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken',
        'Illuminate\Routing\Middleware\SubstituteBindings',
        'Filament\Http\Middleware\DisableBladeIconComponents',
        'Filament\Http\Middleware\DispatchServingFilamentEvent',
    ])
    ->name('filament.admin.auth.login.post');
