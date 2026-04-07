<?php

declare(strict_types=1);

namespace App;

class PricingEngine
{
    private const BASE_FEE = 2.00;
    private const FREE_DISTANCE_KM = 3.0;
    private const MAX_DISTANCE_KM = 10.0;
    private const FEE_PER_KM = 0.50;
    private const HEAVY_WEIGHT_KG = 5.0;
    private const HEAVY_WEIGHT_SUPPLEMENT = 1.50;

    public static function calculateDeliveryFee(float $distance, float $weight): ?float
    {
        if ($distance < 0 || $weight < 0) {
            throw new \InvalidArgumentException('Distance and weight must be non-negative');
        }

        if ($distance > self::MAX_DISTANCE_KM) {
            return null;
        }

        $fee = self::BASE_FEE;

        if ($distance > self::FREE_DISTANCE_KM) {
            $fee += ($distance - self::FREE_DISTANCE_KM) * self::FEE_PER_KM;
        }

        if ($weight > self::HEAVY_WEIGHT_KG) {
            $fee += self::HEAVY_WEIGHT_SUPPLEMENT;
        }

        return round($fee, 2);
    }
}
