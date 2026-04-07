<?php

declare(strict_types=1);

namespace Tests;

use App\PricingEngine;
use PHPUnit\Framework\TestCase;

class OrderTotalTest extends TestCase
{
    public function test_should_calculate_total_without_promo_when_tuesday_15h(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 2],
        ];

        $result = PricingEngine::calculateOrderTotal($items, 5.0, 2.0, null, 15.0, 2);

        $this->assertSame(25.00, $result['subtotal']);
        $this->assertSame(0.00, $result['discount']);
        $this->assertSame(3.00, $result['deliveryFee']);
        $this->assertSame(1.0, $result['surge']);
        $this->assertSame(28.00, $result['total']);
    }

    public function test_should_apply_20_percent_promo_when_code_is_bienvenue20(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 2],
        ];

        $result = PricingEngine::calculateOrderTotal($items, 5.0, 2.0, 'BIENVENUE20', 15.0, 2);

        $this->assertSame(25.00, $result['subtotal']);
        $this->assertSame(5.00, $result['discount']);
        $this->assertSame(3.00, $result['deliveryFee']);
        $this->assertSame(1.0, $result['surge']);
        $this->assertSame(23.00, $result['total']);
    }

    public function test_should_apply_18_surge_on_delivery_only_when_friday_20h(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 2],
        ];

        $result = PricingEngine::calculateOrderTotal($items, 5.0, 2.0, null, 20.0, 5);

        $this->assertSame(25.00, $result['subtotal']);
        $this->assertSame(3.00, $result['deliveryFee']);
        $this->assertSame(1.8, $result['surge']);
        $this->assertSame(30.40, $result['total']);
    }

    public function test_should_throw_when_items_are_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::calculateOrderTotal([], 5.0, 2.0, null, 15.0, 2);
    }

    public function test_should_ignore_item_when_quantity_is_zero(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 2],
            ['name' => 'Dessert', 'price' => 8.00, 'quantity' => 0],
        ];

        $result = PricingEngine::calculateOrderTotal($items, 5.0, 2.0, null, 15.0, 2);

        $this->assertSame(25.00, $result['subtotal']);
        $this->assertSame(28.00, $result['total']);
    }

    public function test_should_throw_when_item_price_is_negative(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => -1.00, 'quantity' => 1],
        ];

        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::calculateOrderTotal($items, 5.0, 2.0, null, 15.0, 2);
    }

    public function test_should_throw_when_order_is_outside_opening_hours(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 1],
        ];

        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::calculateOrderTotal($items, 5.0, 2.0, null, 23.0, 2);
    }

    public function test_should_throw_when_distance_is_out_of_zone(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 1],
        ];

        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::calculateOrderTotal($items, 15.0, 2.0, null, 15.0, 2);
    }

    public function test_should_validate_math_without_promo_as_subtotal_plus_delivery_equals_total(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 2],
        ];

        $result = PricingEngine::calculateOrderTotal($items, 5.0, 2.0, null, 15.0, 2);

        $this->assertSame(
            round($result['subtotal'] + $result['deliveryFee'], 2),
            $result['total']
        );
    }

    public function test_should_return_expected_keys_and_rounded_amounts(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.345, 'quantity' => 2],
            ['name' => 'Drink', 'price' => 3.333, 'quantity' => 1],
        ];

        $result = PricingEngine::calculateOrderTotal($items, 4.2, 2.0, null, 15.0, 2);

        $this->assertArrayHasKey('subtotal', $result);
        $this->assertArrayHasKey('discount', $result);
        $this->assertArrayHasKey('deliveryFee', $result);
        $this->assertArrayHasKey('surge', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertSame(28.02, $result['subtotal']);
        $this->assertSame(2.60, $result['deliveryFee']);
        $this->assertSame(0.00, $result['discount']);
        $this->assertSame(30.62, $result['total']);
    }

    public function test_should_fail_demo_when_asserting_wrong_total_on_purpose(): void
    {
        $items = [
            ['name' => 'Pizza', 'price' => 12.50, 'quantity' => 2],
        ];

        $result = PricingEngine::calculateOrderTotal($items, 5.0, 2.0, null, 15.0, 2);

        $this->assertSame(99.99, $result['total']);
    }
}
