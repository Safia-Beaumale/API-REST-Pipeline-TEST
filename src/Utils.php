<?php

declare(strict_types=1);

namespace App;

class Utils
{
    public static function capitalize(?string $str): string
    {
        if ($str === null || $str === '') {
            return '';
        }

        return ucfirst(strtolower($str));
    }

    public static function calculateAverage(?array $numbers): float
    {
        if ($numbers === null || count($numbers) === 0) {
            return 0.0;
        }

        return round(array_sum($numbers) / count($numbers), 2);
    }

    public static function slugify(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = strtolower($text);
        $text = str_replace("'", '', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text) ?? '';
        $text = preg_replace('/\s+/', '-', $text) ?? '';
        $text = trim($text, ' -');

        return $text;
    }

    public static function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
