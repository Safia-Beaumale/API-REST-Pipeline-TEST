<?php

declare(strict_types=1);

namespace App;

class PromoCodeRepository
{
    public static function getAll(): array
    {
        return [
            [
                'code' => 'BIENVENUE20',
                'type' => 'percentage',
                'value' => 20,
                'minOrder' => 15.00,
                'expiresAt' => '2026-12-31',
            ],
            [
                'code' => 'FIXED5',
                'type' => 'fixed',
                'value' => 5,
                'minOrder' => 20.00,
                'expiresAt' => '2026-12-31',
            ],
            [
                'code' => 'EXPIRED10',
                'type' => 'percentage',
                'value' => 10,
                'minOrder' => 0.00,
                'expiresAt' => '2020-01-01',
            ],
        ];
    }
}
