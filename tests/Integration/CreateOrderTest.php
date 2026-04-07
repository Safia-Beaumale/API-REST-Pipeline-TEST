<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\OrderStore;

class CreateOrderTest extends ApiTestCase
{
    private array $baseOrder = [
        'items' => [['name' => 'Burger', 'price' => 10.0, 'quantity' => 1]],
        'distance' => 3.0,
        'weight' => 1.0,
        'hour' => 14.0,
        'dayOfWeek' => 3,
    ];

    public function test_should_return_201_with_id_when_order_is_valid(): void
    {
        $response = $this->request('POST', '/orders', $this->baseOrder);

        $this->assertSame(201, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertArrayHasKey('id', $body);
        $this->assertIsInt($body['id']);
    }

    public function test_should_be_retrievable_via_get_after_creation(): void
    {
        $postResponse = $this->request('POST', '/orders', $this->baseOrder);
        $id = $this->json($postResponse)['id'];

        $getResponse = $this->request('GET', "/orders/{$id}");

        $this->assertSame(200, $getResponse->getStatusCode());
        $this->assertSame($id, $this->json($getResponse)['id']);
    }

    public function test_should_return_different_ids_for_two_orders(): void
    {
        $response1 = $this->request('POST', '/orders', $this->baseOrder);
        $response2 = $this->request('POST', '/orders', $this->baseOrder);

        $id1 = $this->json($response1)['id'];
        $id2 = $this->json($response2)['id'];

        $this->assertNotSame($id1, $id2);
    }

    public function test_should_return_400_when_order_is_invalid(): void
    {
        $order = array_merge($this->baseOrder, ['items' => []]);

        $response = $this->request('POST', '/orders', $order);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_should_not_save_order_when_validation_fails(): void
    {
        $order = array_merge($this->baseOrder, ['distance' => 20.0]);

        $this->request('POST', '/orders', $order);

        $this->assertSame(0, OrderStore::count());
    }
}
