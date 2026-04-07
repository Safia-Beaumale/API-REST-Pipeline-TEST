<?php

declare(strict_types=1);

namespace Tests;

use App\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function test_should_capitalize_first_letter_when_lowercase_string(): void
    {
        $input = 'hello';

        $result = Utils::capitalize($input);

        $this->assertSame('Hello', $result);
    }

    public function test_should_lowercase_rest_when_uppercase_string(): void
    {
        $input = 'WORLD';

        $result = Utils::capitalize($input);

        $this->assertSame('World', $result);
    }

    public function test_should_return_empty_string_when_empty_string(): void
    {
        $input = '';

        $result = Utils::capitalize($input);

        $this->assertSame('', $result);
    }

    public function test_should_return_empty_string_when_null(): void
    {
        $input = null;

        $result = Utils::capitalize($input);

        $this->assertSame('', $result);
    }

    public function test_should_return_average_when_three_numbers(): void
    {
        $input = [10, 12, 14];

        $result = Utils::calculateAverage($input);

        $this->assertSame(12.0, $result);
    }

    public function test_should_return_value_when_single_element_array(): void
    {
        $input = [15];

        $result = Utils::calculateAverage($input);

        $this->assertSame(15.0, $result);
    }

    public function test_should_return_zero_when_empty_array(): void
    {
        $input = [];

        $result = Utils::calculateAverage($input);

        $this->assertSame(0.0, $result);
    }

    public function test_should_return_average_when_consecutive_numbers(): void
    {
        $input = [10, 11, 12];

        $result = Utils::calculateAverage($input);

        $this->assertSame(11.0, $result);
    }

    public function test_should_return_zero_when_null_array(): void
    {
        $input = null;

        $result = Utils::calculateAverage($input);

        $this->assertSame(0.0, $result);
    }

    public function test_should_slugify_when_mixed_case_with_space(): void
    {
        $input = 'Hello World';

        $result = Utils::slugify($input);

        $this->assertSame('hello-world', $result);
    }

    public function test_should_trim_dashes_when_leading_trailing_spaces(): void
    {
        $input = ' Spaces Everywhere ';

        $result = Utils::slugify($input);

        $this->assertSame('spaces-everywhere', $result);
    }

    public function test_should_remove_apostrophes_when_french_text(): void
    {
        $input = "C'est l'ete !";

        $result = Utils::slugify($input);

        $this->assertSame('cest-lete', $result);
    }

    public function test_should_return_empty_string_when_empty_input(): void
    {
        $input = '';

        $result = Utils::slugify($input);

        $this->assertSame('', $result);
    }

    public function test_should_return_value_when_within_bounds(): void
    {
        $result = Utils::clamp(5.0, 0.0, 10.0);

        $this->assertSame(5.0, $result);
    }

    public function test_should_return_min_when_value_below_min(): void
    {
        $result = Utils::clamp(-5.0, 0.0, 10.0);

        $this->assertSame(0.0, $result);
    }

    public function test_should_return_max_when_value_above_max(): void
    {
        $result = Utils::clamp(15.0, 0.0, 10.0);

        $this->assertSame(10.0, $result);
    }

    public function test_should_return_zero_when_all_bounds_are_zero(): void
    {
        $result = Utils::clamp(0.0, 0.0, 0.0);

        $this->assertSame(0.0, $result);
    }
}
