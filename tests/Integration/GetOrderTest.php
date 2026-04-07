<?php

declare(strict_types=1);

namespace Tests\Integration;

class GetOrderTest extends ApiTestCase
{
    private array $baseOrder = [
        'items' => [['name' => 'Sushi', 'price' => 15.0, 'quantity' => 2]],
        'distance' => 2.0,
        'weight' => 1.5,
        'hour' => 12.0,
        'dayOfWeek' => 1,
    ];

    public function test_should_return_200_with_order_when_id_exists(): void
    {
        $post = $this->request('POST', '/orders', $this->baseOrder);
        $id = $this->json($post)['id'];

        $response = $this->request('GET', "/orders/{$id}");

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_should_return_404_when_id_does_not_exist(): void
    {
        $response = $this->request('GET', '/orders/9999');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function test_should_return_correct_structure_for_existing_order(): void
    {
        $post = $this->request('POST', '/orders', $this->baseOrder);
        $id = $this->json($post)['id'];

        $response = $this->request('GET', "/orders/{$id}");
        $body = $this->json($response);

        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('subtotal', $body);
        $this->assertArrayHasKey('deliveryFee', $body);
        $this->assertArrayHasKey('surge', $body);
        $this->assertArrayHasKey('total', $body);
    }
}
