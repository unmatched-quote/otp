<?php

namespace JustSomeCode\OTP\DTO;

final readonly class HOTPSettings
{
    public function __construct(
        #[\SensitiveParameter(\Attribute::TARGET_PROPERTY)] public string $secret,
        public string $algo = 'sha1'
    ){}
}