<?php

namespace App\Observers;

use App\Models\Marca;

class MarcaObserver
{

    /**
     * Handle the Marca "creating" event.
     * Este método é chamado ANTES que um novo registro seja salvo no banco de dados.
     */
    public function creating(Marca $marca): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (auth()->check()) {
            $marca->cadastrado_por = auth()->id();
            $marca->atualizado_por = auth()->id(); // Também preenche atualizado_por na criação inicial
        }
    }

    /**
     * Handle the Marca "updating" event.
     * Este método é chamado ANTES que um registro existente seja atualizado no banco de dados.
     */
    public function updating(Marca $marca): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (auth()->check()) {
            $marca->atualizado_por = auth()->id();
        }
    }

    /**
     * Handle the Marca "created" event.
     */
    public function created(Marca $marca): void
    {
        //
    }

    /**
     * Handle the Marca "updated" event.
     */
    public function updated(Marca $marca): void
    {
        //
    }

    /**
     * Handle the Marca "deleted" event.
     */
    public function deleted(Marca $marca): void
    {
        //
    }

    /**
     * Handle the Marca "restored" event.
     */
    public function restored(Marca $marca): void
    {
        //
    }

    /**
     * Handle the Marca "force deleted" event.
     */
    public function forceDeleted(Marca $marca): void
    {
        //
    }
}
