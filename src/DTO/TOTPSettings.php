<?php

namespace JustSomeCode\OTP\DTO;

final readonly class TOTPSettings
{
    public function __construct(
        #[\SensitiveParameter(\Attribute::TARGET_PROPERTY)] public string $secret,
        public int $interval = 30,
        public string $algo = 'sha1'
    ){}
}