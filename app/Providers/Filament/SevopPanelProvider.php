<?php

namespace App\Providers\Filament;

use App\Filament\Sevop\Pages; // Assumindo que a páginas específicas para este painel
//use App\Filament\Sevop\Widgets; // Assumindo que o widgets específicos para este painel
use App\Http\Middleware\RedirectNotActiveUser; // Manter este se usuários inativos não devem acessar
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Auth\EditProfile; // Permitir que o usuário edite seu próprio perfil
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup; // Certifique-se de que este import está presente
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Importe seus widgets
use Filament\Widgets;
use App\Filament\Sevop\Widgets\MaintenanceVehiclesTable;
use App\Filament\Sevop\Widgets\ActiveVehiclesOverview;
use App\Filament\Sevop\Widgets\ActiveVehiclesByModelChart;
use App\Filament\Sevop\Widgets\ActiveVehiclesByLocationChart;
use App\Filament\Sevop\Widgets\ActiveVehiclesByLocationTable;
use App\Filament\Sevop\Widgets\ActiveVehiclesByUnitAllocationTable;
use App\Filament\Widgets\FleetEventCrossTabulation;

class SevopPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('sevop') // ID único para este painel
            ->path('sevop') // Rota URL para este painel (ex: /sevop)
            ->login() // Usa o login padrão do Filament ou um customizado se for o caso
            ->profile(EditProfile::class) // Permite que o usuário acesse e edite seu perfil
            ->colors([
                'primary' => Color::Orange, // Escolha uma cor primária diferente para distinguir visualmente
            ])
            // Descobre os recursos específicos deste painel.
            // Eles deverão estar na pasta app/Filament/Sevop/Resources
            ->discoverResources(in: app_path('Filament/Sevop/Resources'), for: 'App\Filament\Sevop\Resources')
            // Descobre as páginas específicas deste painel.
            // Elas deverão estar na pasta app/Filament/Sevop/Pages
            ->discoverPages(in: app_path('Filament/Sevop/Pages'), for: 'App\Filament\Sevop\Pages')
            ->pages([
                Pages\Dashboard::class, // Ou crie um Dashboard personalizado para o Sevop
            ])
            // Descobre os widgets específicos deste painel.
            // Eles deverão estar na pasta app/Filament/Sevop/Widgets
            ->discoverWidgets(in: app_path('Filament/Sevop/Widgets'), for: 'App\Filament\Sevop\Widgets')
            ->widgets([
                // Registre seus widgets aqui
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
                ActiveVehiclesOverview::class,      // Seu primeiro widget
                ActiveVehiclesByModelChart::class,  // Seu segundo widget
                ActiveVehiclesByLocationChart::class, // Seu novo widget
                ActiveVehiclesByLocationTable::class, // Seu novo widget de tabela
                ActiveVehiclesByUnitAllocationTable::class, // Seu widget de tabela (por unidade de alocação)
                MaintenanceVehiclesTable::class, // Seu novo widget de veículos em manutenção
                FleetEventCrossTabulation::class, // Seu novo widget de ocorrências histórico

            ])
            ->sidebarCollapsibleOnDesktop(true)
            ->favicon('https://www.flaticon.com/svg/static/icons/svg/2972/2972413.svg') // Favicon customizado
            ->navigationGroups([
                 NavigationGroup::make('Controle de Frota')
                     ->label('Controle de Frota') // Isso já é o padrão, mas pode ser explícito
                     ->icon('heroicon-o-truck') // Opcional: Adiciona um ícone ao menu pai "Controle de Frota"
                 // Se você tiver outros grupos, defina-os aqui também:
                 // NavigationGroup::make('Gerenciamento de Usuários'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                RedirectNotActiveUser::class, // Mantido: garante que apenas usuários ativos acessem (se necessário)
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
