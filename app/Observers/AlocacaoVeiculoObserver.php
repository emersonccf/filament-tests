<?php

namespace App\Observers;

use App\Models\AlocacaoVeiculo;
use Illuminate\Support\Facades\Auth;

class AlocacaoVeiculoObserver
{
    public function creating(AlocacaoVeiculo $alocacaoVeiculo): void
    {
        if (Auth::check()) {
            $alocacaoVeiculo->cadastrado_por = Auth::id();
            $alocacaoVeiculo->atualizado_por = Auth::id();
        }
    }

    public function updating(AlocacaoVeiculo $alocacaoVeiculo): void
    {
        if (Auth::check()) {
            $alocacaoVeiculo->atualizado_por = Auth::id();
        }
    }
    /**
     * Handle the AlocacaoVeiculo "created" event.
     */
    public function created(AlocacaoVeiculo $alocacaoVeiculo): void
    {
        //
    }

    /**
     * Handle the AlocacaoVeiculo "updated" event.
     */
    public function updated(AlocacaoVeiculo $alocacaoVeiculo): void
    {
        //
    }

    /**
     * Handle the AlocacaoVeiculo "deleted" event.
     */
    public function deleted(AlocacaoVeiculo $alocacaoVeiculo): void
    {
        //
    }

    /**
     * Handle the AlocacaoVeiculo "restored" event.
     */
    public function restored(AlocacaoVeiculo $alocacaoVeiculo): void
    {
        //
    }

    /**
     * Handle the AlocacaoVeiculo "force deleted" event.
     */
    public function forceDeleted(AlocacaoVeiculo $alocacaoVeiculo): void
    {
        //
    }
}
