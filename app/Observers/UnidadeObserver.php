<?php

namespace App\Observers;

use App\Models\Unidade;
use Illuminate\Support\Facades\Auth;

class UnidadeObserver
{
    /**
     * Handle the Unidade "creating" event.
     * Este método é chamado ANTES que um novo registro seja salvo no banco de dados.
     */
    public function creating(Unidade $unidade): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (Auth::check()) {
            $unidade->cadastrado_por = Auth::id();
            $unidade->atualizado_por = Auth::id(); // Também preenche atualizado_por na criação inicial
        }
    }

    /**
     * Handle the Unidade "updating" event.
     * Este método é chamado ANTES que um registro existente seja atualizado no banco de dados.
     */
    public function updating(Unidade $unidade): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (Auth::check()) {
            $unidade->atualizado_por = Auth::id(); // Atualiza com o ID do usuário logado na modificação
        }
    }
    /**
     * Handle the Unidade "created" event.
     */
    public function created(Unidade $unidade): void
    {
        //
    }

    /**
     * Handle the Unidade "updated" event.
     */
    public function updated(Unidade $unidade): void
    {
        //
    }

    /**
     * Handle the Unidade "deleted" event.
     */
    public function deleted(Unidade $unidade): void
    {
        //
    }

    /**
     * Handle the Unidade "restored" event.
     */
    public function restored(Unidade $unidade): void
    {
        //
    }

    /**
     * Handle the Unidade "force deleted" event.
     */
    public function forceDeleted(Unidade $unidade): void
    {
        //
    }
}
