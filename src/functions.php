<?php

namespace JustSomeCode\OTP;

function base32_encode(string $string): string
{
    if(empty($string)) return '';

    $alphabet = base32_get_alphabet();

    $encoded = '';

    $i = $bits = $val = 0;
    $len = strlen($string);

    $string .= str_repeat(chr(0), 4);

    $chars = (array)unpack('C*', $string, 0);

    while($i < $len || 0 !== $bits)
    {
        //If the bit length has fallen below 5, shift left 8 and add the next character.
        if($bits < 5)
        {
            $val = $val << 8;
            $bits += 8;
            $i++;
            $val += $chars[$i];
        }

        $shift = $bits - 5;
        $encoded .= ($i - (int)($bits > 8) > $len && 0 == $val) ? '=' : $alphabet[$val >> $shift];
        $val = $val & ((1 << $shift) - 1);
        $bits -= 5;
    }

    return $encoded;
}

function base32_decode(string $string): string
{
    // Only work in upper cases
    $base32_str = strtoupper($string);

    if(empty($base32_str)) return '';

    $decoded = '';

    $len = strlen($base32_str);
    $n = 0;
    $bitLen = 5;
    $map = base32_get_character_map();
    $val = $map[$base32_str[0]];

    while($n < $len)
    {
        //If the bit length has fallen below 8, shift left 5 and add the next pentet.
        if($bitLen < 8)
        {
            $val = $val << 5;
            $bitLen += 5;
            $n++;
            $pentet = $base32_str[$n] ?? '=';

            //If the new pentet is padding, make this the last iteration.
            if('=' === $pentet)
            {
                $n = $len;
            }

            $val += $map[$pentet];
        }
        else
        {
            $shift = $bitLen - 8;

            $decoded .= chr($val >> $shift);
            $val = $val & ((1 << $shift) - 1);
            $bitLen -= 8;
        }
    }

    return $decoded;
}

function base32_get_alphabet(): string
{
    return 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';
}

function base32_get_character_map(): array
{
    return [
        '=' => 0b00000,
        'A' => 0b00000,
        'B' => 0b00001,
        'C' => 0b00010,
        'D' => 0b00011,
        'E' => 0b00100,
        'F' => 0b00101,
        'G' => 0b00110,
        'H' => 0b00111,
        'I' => 0b01000,
        'J' => 0b01001,
        'K' => 0b01010,
        'L' => 0b01011,
        'M' => 0b01100,
        'N' => 0b01101,
        'O' => 0b01110,
        'P' => 0b01111,
        'Q' => 0b10000,
        'R' => 0b10001,
        'S' => 0b10010,
        'T' => 0b10011,
        'U' => 0b10100,
        'V' => 0b10101,
        'W' => 0b10110,
        'X' => 0b10111,
        'Y' => 0b11000,
        'Z' => 0b11001,
        '2' => 0b11010,
        '3' => 0b11011,
        '4' => 0b11100,
        '5' => 0b11101,
        '6' => 0b11110,
        '7' => 0b11111,
    ];
}

function otp_counter_to_binary(int $counter): string
{
    $hex = dechex($counter);

    if(strlen($hex) > 16)
    {
        throw new \InvalidArgumentException("Counter value too large. Expected: less than 16, got: ". strlen($hex));
    }

    // Zero-fill the string, so it's 16 chars in length = 8 bytes (2 chars = 1 byte), which we'll
    // convert to binary format using pack()
    return pack('H*', str_pad($hex, 16, "0", STR_PAD_LEFT));
}

function otp_truncate(string $hash, int $length = 6): string
{
    $offset = ord($hash[strlen($hash) - 1]) & 0xf;

    $value = (
        ((ord($hash[$offset + 0]) & 0x7f) << 24) |
        ((ord($hash[$offset + 1]) & 0xff) << 16) |
        ((ord($hash[$offset + 2]) & 0xff) << 8) |
        (ord($hash[$offset + 3]) & 0xff)
    );

    return substr($value, -1 * $length);
}