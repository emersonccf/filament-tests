<?php

namespace App\Observers;

use App\Models\HistoricoVeiculo;
use Illuminate\Support\Facades\Auth;

class HistoricoVeiculoObserver
{
    public function creating(HistoricoVeiculo $historicoVeiculo): void
    {
        if (Auth::check()) {
            $historicoVeiculo->cadastrado_por = Auth::id();
            $historicoVeiculo->atualizado_por = Auth::id();
        }
    }

    public function updating(HistoricoVeiculo $historicoVeiculo): void
    {
        if (Auth::check()) {
            $historicoVeiculo->atualizado_por = Auth::id();
        }
    }
    /**
     * Handle the HistoricoVeiculo "created" event.
     */
    public function created(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "updated" event.
     */
    public function updated(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "deleted" event.
     */
    public function deleted(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "restored" event.
     */
    public function restored(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "force deleted" event.
     */
    public function forceDeleted(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }
}
