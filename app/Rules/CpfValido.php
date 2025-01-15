<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class CpfValido implements ValidationRule
{
    protected ?int $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remover caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $value);

        $query = DB::table('users')->where('cpf', $cpf);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        // Verifica se o CPF já exite no banco de dados de usuários
        if ($query->exists()) {
            $fail('Já exite alguém no sistema associado a este CPF.');
            return;
        }


        // Verificar se tem 11 dígitos
        if (strlen($cpf) != 11) {
            $fail('O CPF deve ter 11 dígitos.');
            return;
        }

        // Evitar CPFs conhecidos como inválidos (todos os dígitos iguais)
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O CPF não é válido.');
            return;
        }

        // Validação dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $fail('O CPF não é válido.');
                return;
            }
        }
    }
}
