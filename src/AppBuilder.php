<?php

declare(strict_types=1);

namespace App;

use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AppBuilder
{
    private const JSON_FLAGS = JSON_PRESERVE_ZERO_FRACTION;

    public static function create(): App
    {
        $app = AppFactory::create();
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $app->post('/orders/simulate', function (Request $request, Response $response): Response {
            $data = $request->getParsedBody() ?? [];
            $items = is_array($data['items'] ?? null) ? $data['items'] : [];

            try {
                $result = PricingEngine::calculateOrderTotal(
                    $items,
                    (float) ($data['distance'] ?? 0),
                    (float) ($data['weight'] ?? 0),
                    isset($data['promoCode']) && $data['promoCode'] !== '' ? (string) $data['promoCode'] : null,
                    (float) ($data['hour'] ?? 0),
                    (int) ($data['dayOfWeek'] ?? 0)
                );
                $response->getBody()->write((string) json_encode($result, self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } catch (NotFoundException $e) {
                $response->getBody()->write((string) json_encode(['error' => $e->getMessage()], self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            } catch (\InvalidArgumentException $e) {
                $response->getBody()->write((string) json_encode(['error' => $e->getMessage()], self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        });

        $app->post('/orders', function (Request $request, Response $response): Response {
            $data = $request->getParsedBody() ?? [];
            $items = is_array($data['items'] ?? null) ? $data['items'] : [];

            try {
                $result = PricingEngine::calculateOrderTotal(
                    $items,
                    (float) ($data['distance'] ?? 0),
                    (float) ($data['weight'] ?? 0),
                    isset($data['promoCode']) && $data['promoCode'] !== '' ? (string) $data['promoCode'] : null,
                    (float) ($data['hour'] ?? 0),
                    (int) ($data['dayOfWeek'] ?? 0)
                );
                $order = OrderStore::save($result);
                $response->getBody()->write((string) json_encode($order, self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } catch (NotFoundException $e) {
                $response->getBody()->write((string) json_encode(['error' => $e->getMessage()], self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            } catch (\InvalidArgumentException $e) {
                $response->getBody()->write((string) json_encode(['error' => $e->getMessage()], self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        });

        $app->get('/orders/{id}', function (Request $request, Response $response, array $args): Response {
            $id = (int) $args['id'];
            $order = OrderStore::find($id);

            if ($order === null) {
                $body = (string) json_encode(['error' => "Order {$id} not found"], self::JSON_FLAGS);
                $response->getBody()->write($body);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write((string) json_encode($order, self::JSON_FLAGS));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        });

        $app->post('/promo/validate', function (Request $request, Response $response): Response {
            $data = $request->getParsedBody() ?? [];

            if (!isset($data['promoCode']) || $data['promoCode'] === '') {
                $body = (string) json_encode(['error' => 'promoCode is required'], self::JSON_FLAGS);
                $response->getBody()->write($body);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            if (!isset($data['subtotal'])) {
                $body = (string) json_encode(['error' => 'subtotal is required'], self::JSON_FLAGS);
                $response->getBody()->write($body);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $subtotal = (float) $data['subtotal'];
            $promoCode = (string) $data['promoCode'];

            try {
                $discounted = PricingEngine::applyPromoCode($subtotal, $promoCode, PromoCodeRepository::getAll());
                $result = [
                    'valid' => true,
                    'originalPrice' => $subtotal,
                    'discountedPrice' => $discounted,
                    'discount' => round($subtotal - $discounted, 2),
                ];
                $response->getBody()->write((string) json_encode($result, self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } catch (NotFoundException $e) {
                $response->getBody()->write((string) json_encode(['error' => $e->getMessage()], self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            } catch (\InvalidArgumentException $e) {
                $response->getBody()->write((string) json_encode(['error' => $e->getMessage()], self::JSON_FLAGS));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        });

        return $app;
    }
}
