<?php

namespace JustSomeCode\OTP\Tests\Unit\Functions;

use PHPUnit\Framework\TestCase;
use function JustSomeCode\OTP\base32_encode;

class Base32EncodeTest extends TestCase
{
    public function test_base32_encodes_ok()
    {
        $expected = 'NBSWY3DPEB3W64TMMQWCASJAONUG65LMMQQGEZJAMVXGG33EMVSA====';
        $input = 'hello world, I should be encoded';
        $result = base32_encode($input);

        $this->assertEquals($expected, $result);
    }
}