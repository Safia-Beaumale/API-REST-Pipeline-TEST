<?php

declare(strict_types=1);

namespace Tests;

use App\PricingEngine;
use PHPUnit\Framework\TestCase;

class PromoCodeTest extends TestCase
{
    private array $promoCodes;

    protected function setUp(): void
    {
        $this->promoCodes = [
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
                'code' => 'FIXED10',
                'type' => 'fixed',
                'value' => 10,
                'minOrder' => 0.00,
                'expiresAt' => '2026-12-31',
            ],
            [
                'code' => 'ALL100',
                'type' => 'percentage',
                'value' => 100,
                'minOrder' => 0.00,
                'expiresAt' => '2026-12-31',
            ],
            [
                'code' => 'EXPIRED',
                'type' => 'percentage',
                'value' => 10,
                'minOrder' => 0.00,
                'expiresAt' => '2020-01-01',
            ],
            [
                'code' => 'EXPIRESTODAY',
                'type' => 'fixed',
                'value' => 5,
                'minOrder' => 0.00,
                'expiresAt' => date('Y-m-d'),
            ],
        ];
    }

    public function test_should_apply_percentage_discount_when_valid_code(): void
    {
        $result = PricingEngine::applyPromoCode(50.0, 'BIENVENUE20', $this->promoCodes);

        $this->assertSame(40.0, $result);
    }

    public function test_should_apply_fixed_discount_when_valid_code(): void
    {
        $result = PricingEngine::applyPromoCode(30.0, 'FIXED5', $this->promoCodes);

        $this->assertSame(25.0, $result);
    }

    public function test_should_apply_discount_when_subtotal_equals_min_order(): void
    {
        $result = PricingEngine::applyPromoCode(20.0, 'FIXED5', $this->promoCodes);

        $this->assertSame(15.0, $result);
    }

    public function test_should_throw_when_code_is_expired(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::applyPromoCode(30.0, 'EXPIRED', $this->promoCodes);
    }

    public function test_should_throw_when_subtotal_under_min_order(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::applyPromoCode(10.0, 'BIENVENUE20', $this->promoCodes);
    }

    public function test_should_throw_when_code_is_unknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::applyPromoCode(30.0, 'FAKECODE', $this->promoCodes);
    }

    public function test_should_return_zero_when_fixed_discount_exceeds_subtotal(): void
    {
        $result = PricingEngine::applyPromoCode(5.0, 'FIXED10', $this->promoCodes);

        $this->assertSame(0.0, $result);
    }

    public function test_should_return_zero_when_percentage_is_100(): void
    {
        $result = PricingEngine::applyPromoCode(50.0, 'ALL100', $this->promoCodes);

        $this->assertSame(0.0, $result);
    }

    public function test_should_return_subtotal_when_subtotal_is_zero_and_no_min_order(): void
    {
        $result = PricingEngine::applyPromoCode(0.0, 'ALL100', $this->promoCodes);

        $this->assertSame(0.0, $result);
    }

    public function test_should_return_subtotal_unchanged_when_promo_code_is_null(): void
    {
        $result = PricingEngine::applyPromoCode(30.0, null, $this->promoCodes);

        $this->assertSame(30.0, $result);
    }

    public function test_should_return_subtotal_unchanged_when_promo_code_is_empty(): void
    {
        $result = PricingEngine::applyPromoCode(30.0, '', $this->promoCodes);

        $this->assertSame(30.0, $result);
    }

    public function test_should_throw_when_subtotal_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::applyPromoCode(-10.0, 'BIENVENUE20', $this->promoCodes);
    }

    public function test_should_accept_code_when_it_expires_today(): void
    {
        $result = PricingEngine::applyPromoCode(20.0, 'EXPIRESTODAY', $this->promoCodes);

        $this->assertSame(15.0, $result);
    }
}
