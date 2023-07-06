# One Time Password validation library

This library validates []RFC 4226](https://www.ietf.org/rfc/rfc4226.txt) and [RFC 6238](https://www.ietf.org/rfc/rfc6238.txt) one time passwords and provides values to create QR codes to store
accounts to authenticator apps.

## Installation

`composer require just-some-code/otp`

## Use

### HMAC-based one time password

HMAC-based one time password relies on shared secret and persisting a counter value to storage.

Tradeoff is having to associate an account (secret) with counter value.

```php
use JustSomeCode\OTP\Algorithm\HOTP;
use JustSomeCode\OTP\DTO\HOTPSettings;

$secret = "Hello!";
$counter = 50; // This number should come from database/permanent storage

$settings = new HOTPSettings($secret, $counter);
$otp = new HOTP();

$result = $otp->verify('753566', $counter); // true / false

var_dump($result);
```

### Time-based one time password

Time-based one password relies on shared secret and value of `time()`, which is used as syncing-factor between
user's authenticator app and server.

```php

use JustSomeCode\OTP\Algorithm\TOTP;
use JustSomeCode\OTP\DTO\TOTPSettings;

$secret = "Hello!";
$interval = 30; // Interval, in seconds. This value controls time-intervals during which auto-generated OTPs are valid
$algo = 'sha1'; // Algorithm used for hashing in order to calcualte 6 or 8 digit OTP

$settings = new TOTPSettings($secret, $interval, $algo);
$otp = new TOTP($settings);

// Verify at specific point in time:
$result = $otp->at('540149', 1388534401); // Jan 1st 2014 @ 00:00

// Verify at current time
$result = $otp->verify('123456'); // bool
```

