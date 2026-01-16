<?php

namespace App\Helpers;

class DisplayHelper
{
    /**
     * Safely display a value, showing "N/A" if null or empty
     *
     * @param mixed $value
     * @param string $default
     * @return string
     */
    public static function safeDisplay($value, string $default = 'N/A'): string
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_string($value) && trim($value) === '') {
            return $default;
        }

        return (string) $value;
    }

    /**
     * Safely format a date, showing "N/A" if null
     *
     * @param mixed $date
     * @param string $format
     * @return string
     */
    public static function safeDate($date, string $format = 'M d, Y'): string
    {
        if ($date === null) {
            return 'N/A';
        }

        try {
            if (is_string($date)) {
                $date = new \DateTime($date);
            }
            return $date->format($format);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Safely format a number, showing "N/A" if null
     *
     * @param mixed $number
     * @param int $decimals
     * @return string
     */
    public static function safeNumber($number, int $decimals = 2): string
    {
        if ($number === null || $number === '') {
            return 'N/A';
        }

        return number_format((float) $number, $decimals);
    }
}
