<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public string $userName;
    public array $breadcrumbs;

    public function mount()
    {
        $this->userName = getNomeReduzido(Auth::user()->name);
        $this->breadcrumbs = [
            ['name' => 'Painel de Atividades', 'url' => route('dashboard')],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard',[
                'breadcrumbs' => $this->breadcrumbs
            ]
        )->layout('components.layouts.app', [
            'tituloPagina' => "Painel de Atividades de $this->userName",
            'tituloFormulario'=> '',
            'userName' => $this->userName,
            ]
        );
    }
}
