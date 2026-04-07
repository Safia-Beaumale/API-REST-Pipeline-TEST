<?php

declare(strict_types=1);

namespace Tests;

use App\StudentSorter;
use PHPUnit\Framework\TestCase;

class StudentSorterTest extends TestCase
{
    public function test_should_sort_students_by_grade_ascending(): void
    {
        $students = [
            ['name' => 'Alice', 'grade' => 15, 'age' => 20],
            ['name' => 'Bob', 'grade' => 10, 'age' => 22],
            ['name' => 'Charlie', 'grade' => 18, 'age' => 19],
        ];

        $result = StudentSorter::sortStudents($students, 'grade', 'asc');

        $this->assertSame(10, $result[0]['grade']);
        $this->assertSame(15, $result[1]['grade']);
        $this->assertSame(18, $result[2]['grade']);
    }

    public function test_should_sort_students_by_grade_descending(): void
    {
        $students = [
            ['name' => 'Alice', 'grade' => 15, 'age' => 20],
            ['name' => 'Bob', 'grade' => 10, 'age' => 22],
            ['name' => 'Charlie', 'grade' => 18, 'age' => 19],
        ];

        $result = StudentSorter::sortStudents($students, 'grade', 'desc');

        $this->assertSame(18, $result[0]['grade']);
        $this->assertSame(15, $result[1]['grade']);
        $this->assertSame(10, $result[2]['grade']);
    }

    public function test_should_sort_students_by_name_ascending(): void
    {
        $students = [
            ['name' => 'Charlie', 'grade' => 18, 'age' => 19],
            ['name' => 'Alice', 'grade' => 15, 'age' => 20],
            ['name' => 'Bob', 'grade' => 10, 'age' => 22],
        ];

        $result = StudentSorter::sortStudents($students, 'name', 'asc');

        $this->assertSame('Alice', $result[0]['name']);
        $this->assertSame('Bob', $result[1]['name']);
        $this->assertSame('Charlie', $result[2]['name']);
    }

    public function test_should_sort_students_by_age_ascending(): void
    {
        $students = [
            ['name' => 'Alice', 'grade' => 15, 'age' => 22],
            ['name' => 'Bob', 'grade' => 10, 'age' => 19],
            ['name' => 'Charlie', 'grade' => 18, 'age' => 20],
        ];

        $result = StudentSorter::sortStudents($students, 'age', 'asc');

        $this->assertSame(19, $result[0]['age']);
        $this->assertSame(20, $result[1]['age']);
        $this->assertSame(22, $result[2]['age']);
    }

    public function test_should_return_empty_array_for_null_input(): void
    {
        $result = StudentSorter::sortStudents(null, 'grade');

        $this->assertSame([], $result);
    }

    public function test_should_return_empty_array_for_empty_input(): void
    {
        $result = StudentSorter::sortStudents([], 'grade');

        $this->assertSame([], $result);
    }

    public function test_should_not_modify_the_original_array(): void
    {
        $students = [
            ['name' => 'Alice', 'grade' => 15, 'age' => 20],
            ['name' => 'Bob', 'grade' => 10, 'age' => 22],
        ];
        $original = $students;

        StudentSorter::sortStudents($students, 'grade', 'asc');

        $this->assertSame($original, $students);
    }

    public function test_should_default_to_ascending_order(): void
    {
        $students = [
            ['name' => 'Alice', 'grade' => 15, 'age' => 20],
            ['name' => 'Bob', 'grade' => 10, 'age' => 22],
            ['name' => 'Charlie', 'grade' => 18, 'age' => 19],
        ];

        $result = StudentSorter::sortStudents($students, 'grade');

        $this->assertSame(10, $result[0]['grade']);
        $this->assertSame(15, $result[1]['grade']);
        $this->assertSame(18, $result[2]['grade']);
    }
}
