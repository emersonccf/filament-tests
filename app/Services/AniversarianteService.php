<?php

namespace App\Services;

use App\Models\Pessoa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;


class AniversarianteService
{
    /**
     * Retorna os aniversariantes do mês atual.
     *
     * @return Collection
     */
    public function getAniversariantesDoMes(): Collection
    {
        $mesAtual = Carbon::now()->month;
        // TODO: implantar filtro para exibir apenas as pessoas ativas excluindo: falecidos, exonerados, etc.
        return Pessoa::whereMonth('data_nascimento', $mesAtual)
            ->where('ativo', true)
            ->orderBy(DB::raw('DAY(data_nascimento)'))
            ->select('nome', 'data_nascimento')
            ->get();
    }

    /**
     * Formata os aniversariantes para exibição.
     *
     * @param Collection $aniversariantes
     * @return array
     */
    public function formatarAniversariantes(Collection $aniversariantes) : array
    {
        return $aniversariantes->map(function ($pessoa) {
            $diaNascimento = Carbon::parse($pessoa->data_nascimento)->format('d'); // Retorna o dia com dois dígitos
            $ehAniversarioHoje = Carbon::parse($pessoa->data_nascimento)->isBirthday();

            return [
                'dia' => $diaNascimento,
                'nome' => getNomeReduzido($pessoa->nome),
                'ehAniversarioHoje' => $ehAniversarioHoje
            ];
        })->toArray();
    }
}
