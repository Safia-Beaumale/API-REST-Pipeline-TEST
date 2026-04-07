<?php

declare(strict_types=1);

namespace App;

class StudentSorter
{
    public static function sortStudents(?array $students, string $sortBy, string $order = 'asc'): array
    {
        if ($students === null || count($students) === 0) {
            return [];
        }

        $result = $students;
        usort($result, function ($a, $b) use ($sortBy, $order) {
            $cmp = $a[$sortBy] <=> $b[$sortBy];
            return $order === 'desc' ? -$cmp : $cmp;
        });
        return $result;
    }
}
