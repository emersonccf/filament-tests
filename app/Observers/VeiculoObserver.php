<?php

namespace App\Observers;

use App\Models\Veiculo;
use Illuminate\Support\Facades\Auth; // Importar Auth para pegar o usuário logado

class VeiculoObserver
{
    /**
     * Handle the Veiculo "creating" event.
     * Este método é chamado ANTES que um novo registro seja salvo no banco de dados.
     */
    public function creating(Veiculo $veiculo): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (Auth::check()) {
            $veiculo->cadastrado_por = Auth::id();
            $veiculo->atualizado_por = Auth::id(); // Também preenche atualizado_por na criação inicial
        }
    }

    /**
     * Handle the Veiculo "updating" event.
     * Este método é chamado ANTES que um registro existente seja atualizado no banco de dados.
     */
    public function updating(Veiculo $veiculo): void
    {
        // Verifica se há um usuário logado antes de tentar pegar o ID
        if (Auth::check()) {
            $veiculo->atualizado_por = Auth::id(); // Atualiza com o ID do usuário logado na modificação
        }
    }

    /**
     * Handle the Veiculo "created" event.
     */
    public function created(Veiculo $veiculo): void
    {
        //
    }

    /**
     * Handle the Veiculo "updated" event.
     */
    public function updated(Veiculo $veiculo): void
    {
        //
    }

    /**
     * Handle the Veiculo "deleted" event.
     */
    public function deleted(Veiculo $veiculo): void
    {
        //
    }

    /**
     * Handle the Veiculo "restored" event.
     */
    public function restored(Veiculo $veiculo): void
    {
        //
    }

    /**
     * Handle the Veiculo "force deleted" event.
     */
    public function forceDeleted(Veiculo $veiculo): void
    {
        //
    }
}
