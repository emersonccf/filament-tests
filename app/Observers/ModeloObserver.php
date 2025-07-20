<?php

namespace App\Observers;

use App\Models\Modelo;
use Illuminate\Support\Facades\Auth; // Para acessar o usuário logado

class ModeloObserver
{
    /**
     * Handle the Modelo "creating" event.
     * Este método é chamado ANTES que um novo registro seja salvo no banco de dados.
     */
    public function creating(Modelo $modelo): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (Auth::check()) {
            $modelo->cadastrado_por = Auth::id(); // Preenche com o ID do usuário logado
            $modelo->atualizado_por = Auth::id(); // Também preenche atualizado_por na criação inicial
        }
    }

    /**
     * Handle the Modelo "updating" event.
     * Este método é chamado ANTES que um registro existente seja atualizado no banco de dados.
     */
    public function updating(Modelo $modelo): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (Auth::check()) {
            $modelo->atualizado_por = Auth::id(); // Atualiza com o ID do usuário logado na modificação
        }
    }

    /**
     * Handle the Modelo "created" event.
     */
    public function created(Modelo $modelo): void
    {
        //
    }

    /**
     * Handle the Modelo "updated" event.
     */
    public function updated(Modelo $modelo): void
    {
        //
    }

    /**
     * Handle the Modelo "deleted" event.
     */
    public function deleted(Modelo $modelo): void
    {
        //
    }

    /**
     * Handle the Modelo "restored" event.
     */
    public function restored(Modelo $modelo): void
    {
        //
    }

    /**
     * Handle the Modelo "force deleted" event.
     */
    public function forceDeleted(Modelo $modelo): void
    {
        //
    }
}
