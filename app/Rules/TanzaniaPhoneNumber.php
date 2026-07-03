<?php

namespace App\Rules;

use App\Support\TanzaniaPhoneNumber as PhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TanzaniaPhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!PhoneNumber::isValid(is_string($value) ? $value : null)) {
            $fail('The ' . str_replace('_', ' ', $attribute) . ' must be a valid Tanzania mobile number, for example 0712345678 or +255712345678.');
        }
    }
}
