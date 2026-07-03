<?php

namespace App\Support;

class TanzaniaPhoneNumber
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $phone = preg_replace('/[\s\-()]/', '', trim($value));

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }

        if (preg_match('/^0([67]\d{8})$/', $phone, $matches)) {
            return '+255' . $matches[1];
        }

        if (preg_match('/^255([67]\d{8})$/', $phone, $matches)) {
            return '+255' . $matches[1];
        }

        if (preg_match('/^\+255([67]\d{8})$/', $phone, $matches)) {
            return '+255' . $matches[1];
        }

        return $phone;
    }

    public static function isValid(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }

        return preg_match('/^\+255[67]\d{8}$/', self::normalize($value)) === 1;
    }
}
