<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
            ->brandName('App Encuestas')
            ->renderHook(
                'panels::head.end',
                fn () => '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Buscar el elemento del brand y hacerlo clickeable
                        const brandElement = document.querySelector(".fi-logo, [data-slot=\'logo\'], .fi-sidebar-header");
                        if (brandElement) {
                            brandElement.style.cursor = "pointer";
                            brandElement.addEventListener("click", function() {
                                window.location.href = "/";
                            });
                        }
                        
                        // También buscar por texto "App Encuestas"
                        const textElements = document.querySelectorAll("*");
                        textElements.forEach(el => {
                            if (el.textContent.trim() === "App Encuestas" && el.children.length === 0) {
                                el.style.cursor = "pointer";
                                el.addEventListener("click", function() {
                                    window.location.href = "/";
                                });
                            }
                        });
                    });
                </script>'
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
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
            ]);
    }
}
