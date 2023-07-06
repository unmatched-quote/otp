<?php

namespace JustSomeCode\OTP\Tests\Unit\Functions;

use PHPUnit\Framework\TestCase;
use function JustSomeCode\OTP\base32_decode;

class Base32DecodeTest extends TestCase
{
    public function test_base32_decodes_ok()
    {
        $encoded = 'NBSWY3DPEB3W64TMMQWCASJAONUG65LMMQQGEZJAMVXGG33EMVSA====';
        $expected = 'hello world, I should be encoded';
        $decoded = base32_decode($encoded);

        $this->assertEquals($expected, $decoded);
    }

    public function test_base32_decode_returns_empty_string()
    {
        $encoded = '';
        $expected = '';

        $result = base32_decode($encoded);

        $this->assertEquals($expected, $result);
    }
}