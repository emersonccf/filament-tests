<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContemNumeros implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Verifica se a string contém pelo menos uma número
        if (!preg_match('/[0-9]/', $value)) {
            $fail('A :attribute deve conter pelo menos um número.');
        }
    }
}
