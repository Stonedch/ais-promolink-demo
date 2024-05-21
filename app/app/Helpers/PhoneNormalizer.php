<?php

namespace App\Helpers;

class PhoneNormalizer
{
    public static function normalizePhone(string $phone): ?string
    {
        $phone = mb_eregi_replace('[^\d]', '', $phone);
        $phone = strlen($phone) == 11 ? substr($phone, 1, 11) : $phone;

        if (strlen($phone) != 10) {
            return null;
        }

        return $phone;
    }

    public static function humanizePhone(string $phone)
    {
        if ($normalized = PhoneNormalizer::normalizePhone($phone)) {
            $phone = str_split($normalized, 1);

            $result = '+7 (' . implode(array_slice($phone, 0, 3)) . ') ';
            $result .= implode(array_slice($phone, 3, 3)) . '-';
            $result .= implode(array_slice($phone, 6, 2)) . '-';
            $result .= implode(array_slice($phone, 8, 2));

            return $result;
        }

        return null;
    }
}
