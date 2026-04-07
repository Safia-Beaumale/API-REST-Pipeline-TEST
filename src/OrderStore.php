<?php

declare(strict_types=1);

namespace App;

class OrderStore
{
    private static array $orders = [];
    private static int $nextId = 1;

    public static function save(array $order): array
    {
        $order['id'] = self::$nextId++;
        self::$orders[$order['id']] = $order;
        return $order;
    }

    public static function find(int $id): ?array
    {
        return self::$orders[$id] ?? null;
    }

    public static function count(): int
    {
        return count(self::$orders);
    }

    public static function reset(): void
    {
        self::$orders = [];
        self::$nextId = 1;
    }
}
