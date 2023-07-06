<?php

namespace JustSomeCode\OTP\Algorithm;

use JustSomeCode\OTP\DTO\HOTPSettings;
use function JustSomeCode\OTP\otp_truncate;
use function JustSomeCode\OTP\otp_counter_to_binary;

readonly class HOTP
{
    public function __construct(
        public HOTPSettings $settings
    ){}

    public function getOTP(int $counter = 0, int $digits = 6): string
    {
        $hash = hash_hmac($this->settings->algo, otp_counter_to_binary($counter), $this->settings->secret, true);

        return otp_truncate($hash, $digits);
    }

    public function at(int $position, int $digits = 6): string
    {
        return $this->getOTP(...func_get_args());
    }

    public function verify(string $otp, int $position = 0, int $digits = 6): bool
    {
        $calculated = $this->getOTP($position, $digits);

        return (strcmp($otp, $calculated) === 0);
    }

    public function where(string $otp, int $start, int $window, int $digits = 6): int | false
    {
        if($window < 1)
        {
            throw new \InvalidArgumentException(sprintf("Argument 'window' cannot be lower than int 1. Got %d as value", $window));
        }

        $found = false;

        for($i = $start; $i < $start + $window; $i++)
        {
            if($this->verify($otp, $i, $digits))
            {
                $found = $i;

                break;
            }
        }

        return $found;
    }

    /**
     * This method is used to generate a string used by frontend libraries, typical use-case is to create
     * a QR-code which can be scanned by smartphone and saved to an authenticator-type library
     *
     * Note: secret must be base32-encoded
     */
    public function getProvisioningURI(string $name, string $secret, int $counter = 0): string
    {
        return sprintf("otpauth://hotp/%s?secret=%s&counter=%d", urlencode($name), $secret, $counter);
    }
}