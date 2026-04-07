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
    private const DEFAULT_PROMO_CODES = [
        [
            'code' => 'BIENVENUE20',
            'type' => 'percentage',
            'value' => 20,
            'minOrder' => 15.00,
            'expiresAt' => '2026-12-31',
        ],
    ];

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

    public static function applyPromoCode(float $subtotal, ?string $promoCode, array $promoCodes): float
    {
        if ($subtotal < 0) {
            throw new \InvalidArgumentException('Subtotal must be non-negative');
        }

        if ($promoCode === null || $promoCode === '') {
            return $subtotal;
        }

        $promo = null;
        foreach ($promoCodes as $p) {
            if ($p['code'] === $promoCode) {
                $promo = $p;
                break;
            }
        }

        if ($promo === null) {
            throw new \InvalidArgumentException("Unknown promo code: {$promoCode}");
        }

        $today = new \DateTimeImmutable('today');
        $expiresAt = new \DateTimeImmutable($promo['expiresAt']);

        if ($expiresAt < $today) {
            throw new \InvalidArgumentException("Promo code {$promoCode} has expired");
        }

        if ($subtotal < $promo['minOrder']) {
            throw new \InvalidArgumentException(
                "Subtotal does not meet the minimum order of {$promo['minOrder']}€"
            );
        }

        if ($promo['type'] === 'percentage') {
            $total = $subtotal * (1 - $promo['value'] / 100);
        } else {
            $total = $subtotal - $promo['value'];
        }

        return round(max(0.0, $total), 2);
    }

    public static function calculateSurge(float $hour, int $dayOfWeek): float
    {
        if ($dayOfWeek < 1 || $dayOfWeek > 7) {
            throw new \InvalidArgumentException('dayOfWeek must be between 1 (Monday) and 7 (Sunday)');
        }

        if ($hour < 10.0 || $hour >= 22.0) {
            return 0.0;
        }

        if ($dayOfWeek === 7) {
            return 1.2;
        }

        if (($dayOfWeek === 5 || $dayOfWeek === 6) && $hour >= 19.0) {
            return 1.8;
        }

        if ($dayOfWeek <= 4) {
            if ($hour >= 12.0 && $hour < 13.5) {
                return 1.3;
            }

            if ($hour >= 19.0 && $hour < 21.0) {
                return 1.5;
            }
        }

        return 1.0;
    }

    public static function calculateOrderTotal(
        array $items,
        float $distance,
        float $weight,
        ?string $promoCode,
        float $hour,
        int $dayOfWeek
    ): array {
        if ($items === []) {
            throw new \InvalidArgumentException('Items must not be empty');
        }

        $subtotal = 0.0;
        foreach ($items as $item) {
            $price = (float) ($item['price'] ?? 0.0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($price < 0) {
                throw new \InvalidArgumentException('Item price must be non-negative');
            }

            if ($quantity < 0) {
                throw new \InvalidArgumentException('Item quantity must be non-negative');
            }

            if ($quantity === 0) {
                continue;
            }

            $subtotal += $price * $quantity;
        }

        $subtotal = round($subtotal, 2);
        $discountedSubtotal = self::applyPromoCode($subtotal, $promoCode, self::DEFAULT_PROMO_CODES);
        $discount = round($subtotal - $discountedSubtotal, 2);

        $deliveryFee = self::calculateDeliveryFee($distance, $weight);
        if ($deliveryFee === null) {
            throw new \InvalidArgumentException('Distance is out of delivery zone');
        }

        $surge = self::calculateSurge($hour, $dayOfWeek);
        if ($surge === 0.0) {
            throw new \InvalidArgumentException('Service is closed at this hour');
        }

        $total = round($discountedSubtotal + ($deliveryFee * $surge), 2);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'deliveryFee' => round($deliveryFee, 2),
            'surge' => $surge,
            'total' => $total,
        ];
    }
}
