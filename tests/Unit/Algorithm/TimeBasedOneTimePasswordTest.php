<?php

namespace JustSomeCode\OTP\Tests\Unit\Algorithm;

use PHPUnit\Framework\TestCase;
use JustSomeCode\OTP\Algorithm\TOTP;
use JustSomeCode\OTP\DTO\TOTPSettings;
use function JustSomeCode\OTP\base32_decode;
use function JustSomeCode\OTP\base32_encode;

class TimeBasedOneTimePasswordTest extends TestCase
{
    public function testOneTimePasswordIsValid(): TOTP
    {
        $settings = $this->provideSettingsDTO();

        $totp = new TOTP($settings);

        $this->assertEquals('855783', $totp->at(1));
        $this->assertEquals(540149, $totp->at(1388534401)); // 1.1.2014 @ 00:00:01

        return $totp;
    }

    /**
     * @depends testOneTimePasswordIsValid
     */
    public function testProvisioningUriIsValid(TOTP $totp): void
    {
        $result = $totp->getProvisioningURI('My Application', base32_encode($totp->settings->secret));

        $this->assertEquals('otpauth://totp/My+Application?secret=JDDK4U6G3BJLEZ7Y', $result);
    }

    protected function provideSettingsDTO(): TOTPSettings
    {
        $secret = 'JDDK4U6G3BJLEZ7Y'; // this is base32-encoded "Hello!"

        return new TOTPSettings(
            base32_decode($secret),
            30,
            'sha1'
        );
    }
}