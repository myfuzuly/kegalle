<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Format a Sri Lanka phone number as: +94 XX X XXX XXX
     * Works with any common input: 0706930930, +94706930930, 706930930
     * Returns the original string unchanged if it doesn't look like an LK number.
     */
    public static function format(?string $phone): string
    {
        if (!$phone) return '';

        $digits = preg_replace('/[^0-9]/', '', $phone);

        // Normalise to 9-digit local number (strip country code 94 or leading 0)
        if (strlen($digits) === 11 && str_starts_with($digits, '94')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) !== 9) {
            return $phone; // unrecognised format — return as-is
        }

        return '+94' . $digits;
    }

    /**
     * Format for display inside an href="tel:..." attribute (E.164 format).
     */
    public static function tel(?string $phone): string
    {
        if (!$phone) return '';
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) === 11 && str_starts_with($digits, '94')) {
            return '+' . $digits;
        }
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return '+94' . substr($digits, 1);
        }
        if (strlen($digits) === 9) {
            return '+94' . $digits;
        }
        return $phone;
    }
}
