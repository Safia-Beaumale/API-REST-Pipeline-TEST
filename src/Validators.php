<?php

declare(strict_types=1);

namespace App;

class Validators
{
    public static function isValidEmail(mixed $email): bool
    {
        if (!is_string($email) || $email === '') {
            return false;
        }

        $parts = explode('@', $email);

        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return false;
        }

        return str_contains($parts[1], '.');
    }

    public static function isValidPassword(mixed $password): array
    {
        $errors = [];

        if (!is_string($password) || $password === '') {
            return [
                'valid' => false,
                'errors' => [
                    'Password must be at least 8 characters',
                    'Password must contain at least one uppercase letter',
                    'Password must contain at least one lowercase letter',
                    'Password must contain at least one digit',
                    'Password must contain at least one special character (!@#$%^&*)',
                ],
            ];
        }

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one digit';
        }

        if (!preg_match('/[!@#$%^&*]/', $password)) {
            $errors[] = 'Password must contain at least one special character (!@#$%^&*)';
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    public static function isValidAge(mixed $age): bool
    {
        if (!is_int($age)) {
            return false;
        }

        return $age >= 0 && $age <= 150;
    }
}
