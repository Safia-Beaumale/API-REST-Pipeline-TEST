<?php

declare(strict_types=1);

namespace Tests;

use App\PricingEngine;
use PHPUnit\Framework\TestCase;

class SurgePricingTest extends TestCase
{
    public function test_should_return_normal_multiplier_when_tuesday_afternoon(): void
    {
        $result = PricingEngine::calculateSurge(15.0, 2);

        $this->assertSame(1.0, $result);
    }

    public function test_should_return_lunch_multiplier_when_wednesday_1230(): void
    {
        $result = PricingEngine::calculateSurge(12.5, 3);

        $this->assertSame(1.3, $result);
    }

    public function test_should_return_dinner_multiplier_when_thursday_20h(): void
    {
        $result = PricingEngine::calculateSurge(20.0, 4);

        $this->assertSame(1.5, $result);
    }

    public function test_should_return_weekend_evening_multiplier_when_friday_21h(): void
    {
        $result = PricingEngine::calculateSurge(21.0, 5);

        $this->assertSame(1.8, $result);
    }

    public function test_should_return_sunday_multiplier_when_sunday_afternoon(): void
    {
        $result = PricingEngine::calculateSurge(14.0, 7);

        $this->assertSame(1.2, $result);
    }

    public function test_should_return_normal_when_hour_is_exactly_1130(): void
    {
        $result = PricingEngine::calculateSurge(11.5, 1);

        $this->assertSame(1.0, $result);
    }

    public function test_should_return_dinner_multiplier_when_hour_is_exactly_19h_monday(): void
    {
        $result = PricingEngine::calculateSurge(19.0, 1);

        $this->assertSame(1.5, $result);
    }

    public function test_should_return_weekend_evening_multiplier_when_hour_is_exactly_19h_friday(): void
    {
        $result = PricingEngine::calculateSurge(19.0, 5);

        $this->assertSame(1.8, $result);
    }

    public function test_should_return_closed_when_hour_is_exactly_22h(): void
    {
        $result = PricingEngine::calculateSurge(22.0, 3);

        $this->assertSame(0.0, $result);
    }

    public function test_should_return_closed_when_hour_before_10h(): void
    {
        $result = PricingEngine::calculateSurge(9.0, 3);

        $this->assertSame(0.0, $result);
    }

    public function test_should_return_open_when_hour_is_exactly_10h(): void
    {
        $result = PricingEngine::calculateSurge(10.0, 3);

        $this->assertSame(1.0, $result);
    }

    public function test_should_return_weekend_evening_multiplier_when_saturday_21h(): void
    {
        $result = PricingEngine::calculateSurge(21.0, 6);

        $this->assertSame(1.8, $result);
    }

    public function test_should_return_normal_when_friday_afternoon_before_surge(): void
    {
        $result = PricingEngine::calculateSurge(14.0, 5);

        $this->assertSame(1.0, $result);
    }

    public function test_should_throw_when_day_of_week_is_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingEngine::calculateSurge(15.0, 8);
    }
}
