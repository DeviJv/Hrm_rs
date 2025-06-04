<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
// use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Backups;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Blade;
use App\Filament\Pages\Dashboard as Das;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentAsset;
use Spatie\Backup\BackupDestination\Backup;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider {
    public function panel(Panel $panel): Panel {
        return $panel
            ->profile()
            ->default()
            ->brandName('Quantum')
            ->brandLogo(asset('images/logo 3.svg'))
            ->brandLogoHeight('6.5rem')
            ->favicon(asset('images/logo 3.svg'))
            ->databaseNotifications()
            ->databaseNotificationsPolling(null)
            ->databaseTransactions()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->spa()
            ->unsavedChangesAlerts()
            ->id('admin')
            ->path('')
            // ->collapsedSidebarWidth("2rem")
            ->sidebarWidth("18rem")
            ->sidebarCollapsibleOnDesktop()
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            // ->assets([
            //     asset('js/custom-map.js'), // ✅ injeksi JS langsung
            // ])
            ->renderHook(
                'panels::scripts.after',
                fn() => view('customFooter'), // atau langsung string <script>
            )
            // ->renderHook(
            //     // This line tells us where to render it
            //     'panels::scripts.after',
            //     // This is the view that will be rendered
            //     fn() => view('customFooter'),
            // )
            //     ->renderHook('panels::scripts.after', fn() => Blade::render(<<<'BLADE'
            //     <script>
            //         document.addEventListener('alpine:init', () => {
            //             document.addEventListener('filament-google-maps::ready', () => {
            //                 const marker = window.FilamentGoogleMaps?.markers?.marker;
            //                 if (marker) {
            //                     marker.addListener('click', () => {
            //                         alert('🟢 Marker clicked!');
            //                     });
            //                 }
            //             });
            //         });
            //     </script>
            // BLADE))
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Das::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->plugins([
                FilamentSpatieLaravelBackupPlugin::make()
                    ->noTimeout()
                    ->usingPage(Backups::class),
                GlobalSearchModalPlugin::make()
                    ->associateItemsWithTheirGroups(),
                FilamentApexChartsPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 2,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),

            ])
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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