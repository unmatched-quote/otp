<?php

namespace JustSomeCode\OTP\Algorithm;

use JustSomeCode\OTP\DTO\TOTPSettings;
use function JustSomeCode\OTP\otp_truncate;
use function JustSomeCode\OTP\otp_counter_to_binary;

readonly class TOTP
{
    public function __construct(
        public TOTPSettings $settings
    ){}

    public function getOTP(?int $timestamp, int $digits = 6): string
    {
        $timecode = $this->getTimeCode($timestamp, $this->settings->interval);

        $hash = hash_hmac($this->settings->algo, otp_counter_to_binary($timecode), $this->settings->secret, true);

        return otp_truncate($hash, $digits);
    }

    public function verify(int $otp, ?int $timestamp = null, int $digits = 6): bool
    {
        $calculated = $this->at($timestamp, $digits);

        return (strcmp($otp, $calculated) === 0);
    }

    public function at(?int $timestamp = null, int $digits = 6): string
    {
        return $this->getOTP(...func_get_args());
    }

    public function getTimeCode(?int $timestamp, int $interval): int
    {
        if(empty($timestamp)) $timestamp = time();

        if($timestamp < 0) throw new \InvalidArgumentException('Timestamp must be an integer larger than 0, received: ' . $timestamp);

        if(empty($interval)) throw new \InvalidArgumentException('Interval cannot be 0.');

        if($interval < 0) throw new \InvalidArgumentException('Interval must be an integer larger than 0, received: ' . $interval);

        return (int)($timestamp / $interval);
    }

    /**
     * This method is used to generate a string used by frontend libraries, typical use-case is to create
     * a QR-code which can be scanned by smartphone and saved to an authenticator-type library
     *
     * Note: secret must be base32-encoded
     */
    public function getProvisioningURI(string $name, string $secret): string
    {
        return sprintf("otpauth://totp/%s?secret=%s", urlencode($name), $secret);
    }
}