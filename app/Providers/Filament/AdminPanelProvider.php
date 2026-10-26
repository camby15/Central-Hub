<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\NeedsAttention;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\WelcomeBanner;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Central Hub')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font('Instrument Sans')
            ->colors([
                'primary' => Color::hex('#00B4FF'),
                'success' => Color::hex('#34D399'),
                'warning' => Color::hex('#FBBF24'),
                'danger' => Color::hex('#FB7185'),
                'info' => Color::hex('#38BDF8'),
                'gray' => Color::Slate,
            ])
            ->darkMode(condition: true, isForced: true)
            ->themeSwitcher(false)
            ->navigationGroups([
                NavigationGroup::make('Work')->collapsible(false),
                NavigationGroup::make('Sites')->collapsible(false),
                NavigationGroup::make('Insights'),
                NavigationGroup::make('Admin'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WelcomeBanner::class,
                StatsOverview::class,
                NeedsAttention::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
