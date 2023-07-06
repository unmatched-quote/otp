<?php

namespace JustSomeCode\OTP\Tests\Unit\Algorithm;

use PHPUnit\Framework\TestCase;
use JustSomeCode\OTP\Algorithm\HOTP;
use JustSomeCode\OTP\DTO\HOTPSettings;
use function JustSomeCode\OTP\base32_decode;

class HMACBasedOneTimePasswordTest extends TestCase
{
    /**
     * @dataProvider provideValidOtpsWithPosition
     */
    public function testOtpCheckingSucceeds(string $otp, int $position): void
    {
        $hotp = new HOTP($this->provideSettingsDTO());

        $this->assertEquals($otp, $hotp->at($position));
    }

    public static function provideValidOtpsWithPosition(): array
    {
        return [
            ['855783', 0],
            ['416857', 10],
            ['753566', 100],
            ['406569', 1000],
        ];
    }

    protected function provideSettingsDTO(): HOTPSettings
    {
        $secret = 'JDDK4U6G3BJLEZ7Y';

        return new HOTPSettings(base32_decode($secret));
    }
}