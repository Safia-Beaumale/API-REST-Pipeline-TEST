<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\OrderStore;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;

abstract class ApiTestCase extends TestCase
{
    protected App $app;

    protected function setUp(): void
    {
        $this->app = \App\AppBuilder::create();
        OrderStore::reset();
    }

    protected function request(string $method, string $path, array $body = []): ResponseInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest($method, $path)
            ->withParsedBody($body);

        return $this->app->handle($request);
    }

    protected function json(ResponseInterface $response): array
    {
        return (array) json_decode((string) $response->getBody(), true);
    }
}
