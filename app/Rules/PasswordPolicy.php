<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordPolicy implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasUppercase = preg_match('/[A-Z]/', $value);
        $hasLowercase = preg_match('/[a-z]/', $value);
        $hasNumber = preg_match('/[0-9]/', $value);
        $hasSpecialChar = preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $value);

        if (!$hasUppercase) {
            $fail('The ' . $attribute . ' must contain at least one uppercase letter.');
        }

        if (!$hasLowercase) {
            $fail('The ' . $attribute . ' must contain at least one lowercase letter.');
        }

        if (!$hasNumber) {
            $fail('The ' . $attribute . ' must contain at least one number.');
        }

        if (!$hasSpecialChar) {
            $fail('The ' . $attribute . ' must contain at least one special character (!@#$%^&*()_+-=[]{};\':"\\|,.<>/?).
');
        }
    }
}
