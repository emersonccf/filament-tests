<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public string $userName;
    public string $userPhoto;
    public array $breadcrumbs;

    public function mount()
    {
        $this->userName = getNomeReduzido(Auth::user()->name);
        $this->userPhoto = Auth::user()->profile_photo_path ? Auth::user()->profile_photo_path : 'images/user-avatar.jpg';
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
            'userPhoto' => $this->userPhoto,
            ]
        );
    }

    #[On('profile-photo-updated')]
    public function updateUserPhoto()
    {
        $this->userPhoto = Auth::user()->profile_photo_path
            ? Auth::user()->profile_photo_path
            : 'images/user-avatar.jpg';

        $this->dispatch('user-photo-updated', Storage::url($this->userPhoto));
    }

}
