<?php

declare(strict_types=1);

namespace Tests\Integration;

class PromoValidateTest extends ApiTestCase
{
    public function test_should_return_200_with_discount_details_when_code_is_valid(): void
    {
        $response = $this->request('POST', '/promo/validate', [
            'promoCode' => 'BIENVENUE20',
            'subtotal' => 30.0,
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertTrue($body['valid']);
        $this->assertSame(24.0, $body['discountedPrice']);
        $this->assertSame(6.0, $body['discount']);
    }

    public function test_should_return_400_when_code_is_expired(): void
    {
        $response = $this->request('POST', '/promo/validate', [
            'promoCode' => 'EXPIRED10',
            'subtotal' => 30.0,
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertArrayHasKey('error', $body);
    }

    public function test_should_return_400_when_subtotal_under_minimum(): void
    {
        $response = $this->request('POST', '/promo/validate', [
            'promoCode' => 'BIENVENUE20',
            'subtotal' => 10.0,
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $body = $this->json($response);
        $this->assertArrayHasKey('error', $body);
    }

    public function test_should_return_404_when_code_is_unknown(): void
    {
        $response = $this->request('POST', '/promo/validate', [
            'promoCode' => 'FAKECODE',
            'subtotal' => 30.0,
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function test_should_return_400_when_promo_code_is_missing_from_body(): void
    {
        $response = $this->request('POST', '/promo/validate', [
            'subtotal' => 30.0,
        ]);

        $this->assertSame(400, $response->getStatusCode());
    }
}
