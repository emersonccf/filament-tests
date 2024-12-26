<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContemLetrasMinusculas implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Verifica se a string contém pelo menos uma letra minuscula
        if (!preg_match('/[a-z]/', $value)) {
            $fail('A :attribute deve conter pelo menos uma letra minuscula.');
        }
    }
}
