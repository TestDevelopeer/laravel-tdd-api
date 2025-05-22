<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    #[Test]
    public function that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function is_string_returned_of_name_method(): void {
        $str = Str::random(12);
        $this->assertIsString($str);
    }
}
