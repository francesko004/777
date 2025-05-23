<?php
namespace App\Providers\Filament;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Pages\SyncGames;
use App\Filament\Pages\GamesKeyPage;
use App\Filament\Pages\GatewayPage;
use App\Filament\Pages\LayoutCssCustom;
use App\Filament\Pages\SettingMailPage;
use App\Filament\Resources\AffiliateWithdrawResource;
use App\Filament\Resources\BannerResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\DepositResource;
use App\Filament\Resources\GameResource;
use App\Filament\Resources\MissionResource;
use App\Filament\Resources\ProviderResource;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\WalletResource;
use App\Filament\Resources\WithdrawalResource;
use App\Livewire\WalletOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\DashboardAdmin;
// NEW FEATURES 
use App\Filament\Resources\PromotionResource; // Added
use App\Filament\Resources\CupomResource; // Added
use App\Filament\Resources\VipResource; // Added
use App\Filament\Resources\DistributionSystemResource; // Added
use App\Filament\Resources\MinesConfigResource; // Added
use App\Filament\Resources\DailyBonusConfigResource; // Added
use App\Filament\Resources\GameOpenConfigResource; // Added

class AdminPanelProvider extends PanelProvider
{
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls total access to the page
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls visibility of specific elements
    }

    /**
     * @param Panel $panel
     * @return Panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path(env("FILAMENT_BASE_URL"))
            ->login()
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Roboto Condensed')
            ->brandLogo(fn () => view('filament.components.logo'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                DashboardAdmin::class,
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(true)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WalletOverview::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            NavigationItem::make('dashboard')
                                ->icon('heroicon-o-home')
                                ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                                ->url(fn (): string => DashboardAdmin::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.pages.dashboard')),
                        ]),
                    NavigationGroup::make('PLATFORM SETTINGS')
                        ->items([
                            NavigationItem::make('settings')
                                ->icon('heroicon-o-cog')
                                ->label(fn (): string => 'PLATFORM SETTINGS')
                                ->url(fn (): string => SettingResource::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.pages.settings')),
                            NavigationItem::make('custom-layout')
                                ->icon('heroicon-o-paint-brush')
                                ->label(fn (): string => 'CSS AND PIXEL SETTINGS')
                                ->url(fn (): string => LayoutCssCustom::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.pages.layout-css-custom')),
                            NavigationItem::make('gateway')
                                ->icon('heroicon-o-credit-card')
                                ->label(fn (): string => 'PAYMENT SETTINGS')
                                ->url(fn (): string => GatewayPage::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.pages.gateway-page')),
                            NavigationItem::make('games-key')
                                ->icon('heroicon-o-cpu-chip')
                                ->label(fn (): string => 'GAME API SETTINGS')
                                ->url(fn (): string => GamesKeyPage::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.pages.games-key-page')),
                            ...BannerResource::getNavigationItems(),
                            NavigationItem::make('setting-mail')
                                ->icon('heroicon-o-inbox-stack')
                                ->label(fn (): string => 'EMAIL SETTINGS')
                                ->url(fn (): string => SettingMailPage::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.pages.setting-mail-page')),
                        ]),
                    NavigationGroup::make('PLATFORM PROMOTIONS')  // New promotions group
                        ->items([
                            ...CupomResource::getNavigationItems(),  // Adds the Cupom resource to the group
                            ...PromotionResource::getNavigationItems(),  // Adds the Promotion resource to the group
                            ...MissionResource::getNavigationItems(),
                            ...VipResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('PLATFORM MANAGEMENT')
                        ->items([
                            ...UserResource::getNavigationItems(),
                            ...WalletResource::getNavigationItems(),
                            ...DepositResource::getNavigationItems(),
                            ...DistributionSystemResource::getNavigationItems(),
                            ...DailyBonusConfigResource::getNavigationItems(),
                            ...GameOpenConfigResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('WITHDRAWALS')
                        ->items([
                            NavigationItem::make('withdraw_affiliates')
                                ->icon('heroicon-o-banknotes')
                                ->label(fn (): string => 'AFFILIATE WITHDRAWALS')
                                ->url(fn (): string => AffiliateWithdrawResource::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.sub-affiliates.index')),
                            ...WithdrawalResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('GAMES')
                        ->items([
                            // bx NavigationItem::make(label: 'sync-games')
                            //   ->icon('heroicon-o-arrow-path')
                            //   ->label(fn (): string => 'Synchronize Games')
                            //   ->url(fn (): string => SyncGames::getUrl())
                            //   ->isActiveWhen(fn () => request()->routeIs('filament.pages.sync-games')),
                            ...CategoryResource::getNavigationItems(),
                            ...ProviderResource::getNavigationItems(),
                            ...GameResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Optimization')
                        ->label('SYSTEM')
                        ->items([
                            NavigationItem::make('CLEAR CACHE')
                                ->url(url('/clear'), shouldOpenInNewTab: false)
                                ->icon('heroicon-o-trash'),
                        ]),
                ]);
            })
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
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make());
    }
}
