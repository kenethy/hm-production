<?php

namespace App\Providers\Filament;

use App\Filament\Resources\BlogCategoryResource;
use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\BlogTagResource;
use App\Filament\Resources\BookingResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\GalleryCategoryResource;
use App\Filament\Resources\GalleryResource;
use App\Filament\Resources\MechanicReportResource;
use App\Filament\Resources\MechanicResource;
use App\Filament\Resources\PromoResource;
use App\Filament\Resources\ServiceResource;
use App\Http\Middleware\CheckUserRole;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                // Resources available to all users (both admin and staff)
                BookingResource::class,
                ServiceResource::class,

                // Resources available only to admin users
                CustomerResource::class,
                MechanicResource::class,
                PromoResource::class,
                GalleryResource::class,
                GalleryCategoryResource::class,
                BlogPostResource::class,
                BlogCategoryResource::class,
                BlogTagResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // Kita akan menangani pembatasan akses dengan cara lain
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Servis & Booking'),
                NavigationGroup::make()
                    ->label('Konten Website'),
                NavigationGroup::make()
                    ->label('Manajemen Pelanggan'),
            ])
            ->authGuard('web')
            ->renderHook(
                'panels::resource.pages.list-records.table.before',
                function () {
                    $user = Auth::user();
                    if ($user && $user->role === 'staff') {
                        return '<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                            <span class="font-medium">Akses Terbatas!</span> Anda memiliki akses terbatas hanya untuk mengelola Servis dan Booking.
                        </div>';
                    }
                    return '';
                }
            );
    }
}
