<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContemCaracteresEspeciais implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Verifica se a string contém pelo menos um caractere especial
        if (!preg_match('/[\W_]/', $value)) {
            $fail('A :attribute deve conter pelo menos um caractere especial.');
        }
    }
}
