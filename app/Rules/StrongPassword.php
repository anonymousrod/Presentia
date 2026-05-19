<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail("Le mot de passe doit être une chaîne de caractères.");
            return;
        }

        if (strlen($value) < 8) {
            $fail("Le mot de passe doit contenir au moins 8 caractères.");
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $fail("Le mot de passe doit contenir au moins une lettre majuscule.");
        }

        if (!preg_match('/[0-9]/', $value)) {
            $fail("Le mot de passe doit contenir au moins un chiffre.");
        }

        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail("Le mot de passe doit contenir au moins un caractère spécial (ex: @, $, !, %, *, ?, &, #, ?, -, _).");
        }
    }
}
