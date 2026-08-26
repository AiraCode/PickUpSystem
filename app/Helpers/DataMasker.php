<?php

namespace App\Helpers;

class DataMasker
{
    /**
     * Mask Phone Number: e.g. 082139308270 -> 0821****8270
     */
    public static function phone(?string $phone): ?string
    {
        if (!$phone) {
            return $phone;
        }

        $length = strlen($phone);
        if ($length <= 8) {
            $prefixLength = min(2, (int) floor($length / 3));
            $suffixLength = min(2, (int) floor($length / 3));
            $maskLength = max(1, $length - $prefixLength - $suffixLength);
            return substr($phone, 0, $prefixLength) . str_repeat('*', $maskLength) . substr($phone, -$suffixLength);
        }

        $prefix = substr($phone, 0, 4);
        $suffix = substr($phone, -4);
        $maskedLength = max(1, $length - 8);

        return $prefix . str_repeat('*', $maskedLength) . $suffix;
    }

    /**
     * Mask Bank Account Number: e.g. 123456789654324 -> 1234*****324
     */
    public static function accountNumber(?string $accountNumber): ?string
    {
        if (!$accountNumber) {
            return $accountNumber;
        }

        $length = strlen($accountNumber);
        if ($length <= 7) {
            $prefixLength = min(2, (int) floor($length / 3));
            $suffixLength = min(2, (int) floor($length / 3));
            $maskLength = max(1, $length - $prefixLength - $suffixLength);
            return substr($accountNumber, 0, $prefixLength) . str_repeat('*', $maskLength) . substr($accountNumber, -$suffixLength);
        }

        $prefix = substr($accountNumber, 0, 4);
        $suffix = substr($accountNumber, -3);
        $maskedLength = max(1, $length - 7);

        return $prefix . str_repeat('*', $maskedLength) . $suffix;
    }

    /**
     * Mask Bank Account Name: e.g. HARIYANTO OKNES -> H******* O****
     */
    public static function accountName(?string $name): ?string
    {
        if (!$name) {
            return $name;
        }

        $words = explode(' ', trim($name));
        $maskedWords = array_map(function ($word) {
            $len = mb_strlen($word);
            if ($len <= 1) {
                return $word;
            }
            $firstChar = mb_substr($word, 0, 1);
            return $firstChar . str_repeat('*', $len - 1);
        }, $words);

        return implode(' ', $maskedWords);
    }
}
