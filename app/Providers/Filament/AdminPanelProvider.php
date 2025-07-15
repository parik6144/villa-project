<?php

namespace App\Providers\Filament;

use App\Filament\Resources\AdminResource\Widgets\UserOverview;
use App\Filament\Resources\HotelsResource;
use App\Filament\Resources\PriceTypesResource;
use App\Filament\Resources\PropertyHotelsResource;
use App\Filament\Resources\PropertyPricesResource;
use App\Filament\Resources\ReviewsResource;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\PropertyPrices;
use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\PropertyResource;
use App\Filament\Resources\PropertyTypeResource;
use App\Filament\Resources\AttributeResource;
use App\Filament\Resources\AttributeGroupResource;
use App\Filament\Resources\MediaResource;
use App\Filament\Resources\SeasonResource;
use App\Filament\Pages\ManagePropertySettings;
use App\Models\PropertyType;
use App\Models\Property;
use Filament\Support\Assets\Js;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('backend')
            ->path('backend')
            //->login()
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
                UserOverview::class,
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
                EnsureEmailIsVerified::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureEmailIsVerified::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $count = 0;

                if (Auth::check()) {
                    if (Auth::user()->hasRole('admin')) {
                        $count = Property::where('approval_status', 'pending')->count();
                    }
                }

                $navigationItems = [];
                $navigationGroups = [];

                if (Auth::check() && Auth::user()->hasRole('admin')) {
                    $navigationItems[] = NavigationItem::make('Dashboard')
                        ->url('/backend')
                        ->icon('heroicon-o-home');

                    $navigationGroups[] = NavigationGroup::make('Properties')
                        ->items([
                            PropertyResource::getNavigationItems()[0]
                                ->badge($count > 0 ? $count : null),
                            ...PropertyTypeResource::getNavigationItems(),
                            ...AttributeResource::getNavigationItems(),
                            ...AttributeGroupResource::getNavigationItems(),
                            ...SeasonResource::getNavigationItems(),
                            ...HotelsResource::getNavigationItems(),
                            ...PropertyHotelsResource::getNavigationItems(),
                            ...ReviewsResource::getNavigationItems(),
                            NavigationItem::make('Settings')
                                ->url(route('filament.backend.pages.property-settings'))
                                ->icon('heroicon-o-cog'),
                        ]);

                    $navigationGroups[] = NavigationGroup::make('Users')
                        ->items([
                            NavigationItem::make('Users')
                                ->url('/backend/users')
                                ->icon('heroicon-o-users'),
                            NavigationItem::make('Roles')
                                ->url('/backend/extended-roles')
                                ->icon('heroicon-o-shield-check'),
                        ]);

                    $navigationGroups[] = NavigationGroup::make('Companies')
                        ->items([
                            NavigationItem::make('Companies')
                                ->url('/backend/companies')
                                ->icon('heroicon-o-building-library'),
                        ]);

                    $navigationGroups[] = NavigationGroup::make('Planyo')
                        ->items([
                            NavigationItem::make('Reservatios')
                                ->url('/backend/reservations')
                                ->icon('heroicon-o-calendar'),
                        ]);

                    $navigationGroups[] = NavigationGroup::make('Services')
                        ->items([
                            NavigationItem::make('Services')
                                ->url('/backend/services')
                                ->icon('heroicon-o-briefcase'),
                            NavigationItem::make('Service Categories')
                                ->url('/backend/service-categories')
                                ->icon('heroicon-o-building-office-2'),
                        ]);

                    $navigationGroups[] = NavigationGroup::make('Logs')
                        ->items([
                            NavigationItem::make('Activity Logs')
                                ->url('/backend/activitylogs')
                                ->icon('heroicon-o-information-circle'),
                        ]);
                }

                if ((Auth::user()->hasRole(['property_owner', 'manager', 'agent']))) {
                    $navigationGroups[] = NavigationGroup::make('Properties')
                        ->items([
                            PropertyResource::getNavigationItems()[0]
                                ->badge($count > 0 ? $count : null),
                            ...SeasonResource::getNavigationItems(),

                        ]);
                    $navigationGroups[] = NavigationGroup::make('Planyo')
                        ->items([
                            NavigationItem::make('Reservatios')
                                ->url('/backend/reservations')
                                ->icon('heroicon-o-calendar'),
                        ]);
                }

                if ((Auth::user()->hasRole(['property_owner']))) {
                    $navigationGroups[] = NavigationGroup::make('User Settings')
                        ->items([
                            NavigationItem::make('User settings')
                                ->url('/backend/users/' . Auth::id() . '/edit')
                                ->icon('heroicon-o-user'),
                        ]);
                }

                return $builder
                    ->items($navigationItems)
                    ->groups($navigationGroups);
            })
            ->plugins([
                FilamentSpatieRolesPermissionsPlugin::make(),
                ActivitylogPlugin::make()
                    ->navigationGroup('Activity Log')
                    ->navigationCountBadge(true)
                    ->authorize(
                        fn() => auth()->user()->hasRole('admin')
                    )
                    ->navigationSort(3),
            ])
            ->viteTheme('resources/css/filament/backend/theme.css')
            ->assets([
                Js::make('custom-script', resource_path('js/filament_custom.js')),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->unsavedChangesAlerts()
        ;
    }
}
 