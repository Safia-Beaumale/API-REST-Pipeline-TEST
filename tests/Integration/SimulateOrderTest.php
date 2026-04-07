<?php

declare(strict_types=1);

namespace Tests\Integration;

class SimulateOrderTest extends ApiTestCase
{
    private array $baseOrder = [
        'items' => [['name' => 'Pizza', 'price' => 12.50, 'quantity' => 2]],
        'distance' => 5.0,
        'weight' => 2.0,
        'hour' => 15.0,
        'dayOfWeek' => 2,
    ];

    public function test_should_return_200_with_price_detail_for_normal_order(): void
    {
        $response = $this->request('POST', '/orders/simulate', $this->baseOrder);

        $this->assertSame(200, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertArrayHasKey('subtotal', $body);
        $this->assertArrayHasKey('deliveryFee', $body);
        $this->assertArrayHasKey('surge', $body);
        $this->assertArrayHasKey('total', $body);
        $this->assertSame(25.0, $body['subtotal']);
    }

    public function test_should_return_200_with_discount_when_valid_promo_code(): void
    {
        $order = array_merge($this->baseOrder, ['promoCode' => 'BIENVENUE20']);

        $response = $this->request('POST', '/orders/simulate', $order);

        $this->assertSame(200, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertSame(5.0, $body['discount']);
    }

    public function test_should_return_400_when_promo_code_is_expired(): void
    {
        $order = array_merge($this->baseOrder, ['promoCode' => 'EXPIRED10']);

        $response = $this->request('POST', '/orders/simulate', $order);

        $this->assertSame(400, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertArrayHasKey('error', $body);
    }

    public function test_should_return_400_when_items_are_empty(): void
    {
        $order = array_merge($this->baseOrder, ['items' => []]);

        $response = $this->request('POST', '/orders/simulate', $order);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_should_return_400_when_distance_exceeds_10km(): void
    {
        $order = array_merge($this->baseOrder, ['distance' => 15.0]);

        $response = $this->request('POST', '/orders/simulate', $order);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_should_return_400_when_service_is_closed(): void
    {
        $order = array_merge($this->baseOrder, ['hour' => 23.0]);

        $response = $this->request('POST', '/orders/simulate', $order);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_should_apply_surge_multiplier_on_friday_evening(): void
    {
        $order = array_merge($this->baseOrder, ['hour' => 20.0, 'dayOfWeek' => 5]);

        $response = $this->request('POST', '/orders/simulate', $order);

        $this->assertSame(200, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertSame(1.8, $body['surge']);
    }
}
