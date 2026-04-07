<?php

declare(strict_types=1);

namespace Tests;

use App\PricingEngine;
use PHPUnit\Framework\TestCase;

class PricingEngineTest extends TestCase
{
    public function test_should_return_base_fee_when_distance_under_3km(): void
    {
        $result = PricingEngine::calculateDeliveryFee(2.0, 1.0);

        $this->assertSame(2.00, $result);
    }

    public function test_should_add_distance_fee_when_distance_between_3_and_10km(): void
    {
        $result = PricingEngine::calculateDeliveryFee(7.0, 3.0);

        $this->assertSame(4.00, $result);
    }

    public function test_should_add_heavy_supplement_when_weight_above_5kg(): void
    {
        $result = PricingEngine::calculateDeliveryFee(5.0, 8.0);

        $this->assertSame(4.50, $result);
    }

    public function test_should_return_base_fee_when_distance_exactly_3km(): void
    {
        $result = PricingEngine::calculateDeliveryFee(3.0, 1.0);

        $this->assertSame(2.00, $result);
    }

    public function test_should_accept_and_calculate_when_distance_exactly_10km(): void
    {
        $result = PricingEngine::calculateDeliveryFee(10.0, 1.0);

        $this->assertSame(5.50, $result);
    }

    public function test_should_not_add_supplement_when_weight_exactly_5kg(): void
    {
        $result = PricingEngine::calculateDeliveryFee(2.0, 5.0);

        $this->assertSame(2.00, $result);
    }

    public function test_should_return_null_when_distance_over_10km(): void
    {
        $result = PricingEngine::calculateDeliveryFee(15.0, 1.0);

        $this->assertNull($result);
    }

    public function test_should_throw_when_distance_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::calculateDeliveryFee(-1.0, 1.0);
    }

    public function test_should_throw_when_weight_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::calculateDeliveryFee(2.0, -1.0);
    }

    public function test_should_return_base_fee_when_distance_is_zero(): void
    {
        $result = PricingEngine::calculateDeliveryFee(0.0, 1.0);

        $this->assertSame(2.00, $result);
    }

    public function test_should_calculate_correctly_for_6km_2kg(): void
    {
        $result = PricingEngine::calculateDeliveryFee(6.0, 2.0);

        $this->assertSame(3.50, $result);
    }

    public function test_should_calculate_correctly_for_10km_6kg(): void
    {
        $result = PricingEngine::calculateDeliveryFee(10.0, 6.0);

        $this->assertSame(7.00, $result);
    }
}
