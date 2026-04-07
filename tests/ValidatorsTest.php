<?php

declare(strict_types=1);

namespace Tests;

use App\Validators;
use PHPUnit\Framework\TestCase;

class ValidatorsTest extends TestCase
{
    public function test_should_return_true_when_valid_standard_email(): void
    {
        $result = Validators::isValidEmail('user@example.com');

        $this->assertTrue($result);
    }

    public function test_should_return_true_when_email_with_tag_and_subdomain(): void
    {
        $result = Validators::isValidEmail('user.name+tag@domain.co');

        $this->assertTrue($result);
    }

    public function test_should_return_false_when_no_at_sign(): void
    {
        $result = Validators::isValidEmail('invalid');

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_local_part_is_empty(): void
    {
        $result = Validators::isValidEmail('@domain.com');

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_domain_is_absent(): void
    {
        $result = Validators::isValidEmail('user@');

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_empty_string_email(): void
    {
        $result = Validators::isValidEmail('');

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_null_email(): void
    {
        $result = Validators::isValidEmail(null);

        $this->assertFalse($result);
    }

    public function test_should_return_valid_true_when_password_meets_all_rules(): void
    {
        $result = Validators::isValidPassword('Passw0rd!');

        $this->assertTrue($result['valid']);
        $this->assertSame([], $result['errors']);
    }

    public function test_should_return_error_length_when_password_too_short(): void
    {
        $result = Validators::isValidPassword('short');

        $this->assertFalse($result['valid']);
        $this->assertContains('Password must be at least 8 characters', $result['errors']);
        $this->assertContains('Password must contain at least one uppercase letter', $result['errors']);
        $this->assertContains('Password must contain at least one digit', $result['errors']);
        $this->assertContains('Password must contain at least one special character (!@#$%^&*)', $result['errors']);
    }

    public function test_should_return_error_uppercase_when_no_uppercase(): void
    {
        $result = Validators::isValidPassword('alllowercase1!');

        $this->assertFalse($result['valid']);
        $this->assertContains('Password must contain at least one uppercase letter', $result['errors']);
    }

    public function test_should_return_error_lowercase_when_no_lowercase(): void
    {
        $result = Validators::isValidPassword('ALLUPPERCASE1!');

        $this->assertFalse($result['valid']);
        $this->assertContains('Password must contain at least one lowercase letter', $result['errors']);
    }

    public function test_should_return_error_digit_when_no_digit(): void
    {
        $result = Validators::isValidPassword('NoDigits!here');

        $this->assertFalse($result['valid']);
        $this->assertContains('Password must contain at least one digit', $result['errors']);
    }

    public function test_should_return_error_special_when_no_special_char(): void
    {
        $result = Validators::isValidPassword('NoSpecial1here');

        $this->assertFalse($result['valid']);
        $this->assertContains('Password must contain at least one special character (!@#$%^&*)', $result['errors']);
    }

    public function test_should_return_all_errors_when_empty_string_password(): void
    {
        $result = Validators::isValidPassword('');

        $this->assertFalse($result['valid']);
        $this->assertCount(5, $result['errors']);
    }

    public function test_should_return_all_errors_when_null_password(): void
    {
        $result = Validators::isValidPassword(null);

        $this->assertFalse($result['valid']);
        $this->assertCount(5, $result['errors']);
    }

    public function test_should_return_true_when_age_is_25(): void
    {
        $result = Validators::isValidAge(25);

        $this->assertTrue($result);
    }

    public function test_should_return_true_when_age_is_0(): void
    {
        $result = Validators::isValidAge(0);

        $this->assertTrue($result);
    }

    public function test_should_return_true_when_age_is_150(): void
    {
        $result = Validators::isValidAge(150);

        $this->assertTrue($result);
    }

    public function test_should_return_false_when_age_is_negative(): void
    {
        $result = Validators::isValidAge(-1);

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_age_exceeds_150(): void
    {
        $result = Validators::isValidAge(151);

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_age_is_float(): void
    {
        $result = Validators::isValidAge(25.5);

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_age_is_string(): void
    {
        $result = Validators::isValidAge('25');

        $this->assertFalse($result);
    }

    public function test_should_return_false_when_age_is_null(): void
    {
        $result = Validators::isValidAge(null);

        $this->assertFalse($result);
    }
}
