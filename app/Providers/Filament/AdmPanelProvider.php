<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Register;
use App\Http\Middleware\RedirectNotActiveUser;
use App\Http\Middleware\RedirectNotAdminUser;
use App\Http\Response\CustomLoginResponse;
use App\Http\Response\CustomLogoutResponse;
use App\Providers\Filament\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdmPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->bootUsing(function ($panel) {
                app()->bind(LoginResponse::class, CustomLoginResponse::class);
                app()->bind(LogoutResponse::class, CustomLogoutResponse::class);
            })
            ->default()
            ->id('adm')
            ->path('adm')
            ->login(Login::class)
            ->profile(EditProfile::class)
            ->spa()
            ->registration(Register::class)
            ->emailVerification()
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
                //Widgets\FilamentInfoWidget::class,
            ])
            ->sidebarCollapsibleOnDesktop(true)
//            ->favicon('https://icons.iconarchive.com/icons/aha-soft/free-large-boss/512/Admin-icon.png')
            ->favicon('https://icon-library.com/images/admin-user-icon/admin-user-icon-5.jpg')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                EnsureEmailIsVerified::class, //verificação de e-mail
                RedirectNotActiveUser::class, //cuidado com a ordem dos middlewares, essa ordem é importante
                RedirectNotAdminUser::class, //cuidado com a ordem dos middlewares, essa ordem é importante
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
