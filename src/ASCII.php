<?php
/*
 * This file is a part of "charcoal-dev/charset-validator" package.
 * https://github.com/charcoal-dev/charset-validator
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/charset-validator/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Charsets;

/**
 * Class ASCII
 * for 7-bit ASCII Charset
 * @package Charcoal\Charsets
 */
class ASCII
{
    /**
     * Returns TRUE if input argument string is all printable 7-bit ASCII characters (decimal range 32 to 127)
     * @param string $in
     * @return bool
     */
    public static function isPrintableOnly(string $in): bool
    {
        return (bool)preg_match('/^[\x20-\x7E]*$/', $in);
    }

    /**
     * Returns TRUE if all characters in argument string are 7-bit ASCII characters
     * @param string $in
     * @return bool
     */
    public static function Charset(string $in): bool
    {
        return (bool)preg_match('/^[\x00-\x7F]*$/', $in);
    }

    /**
     * This method first strips any characters having ord value height than 127 in ASCII table, while other
     * non-printable 7-bit characters may be optionally kept (ASCII range < 32, such as line breaks or NULL bytes)
     * if provided in "$allowedLowChars", also any printable 7-bit char (ASCII range 32-127) may be stripped
     * if provided in "$stripChars"
     * @param string $value
     * @param string|null $allowedLowChars characters to keep that are below < 32 in ASCII table
     * @param string|null $stripChars characters to strip that are between 32-127 range
     * @return string
     */
    public static function Filter(string $value, ?string $allowedLowChars = null, ?string $stripChars = null): string
    {
        $clean = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH); // Remove all chars > 127
        $allowed = [];
        if ($allowedLowChars) {
            $allowedStrLen = strlen($allowedLowChars);
            for ($i = 0; $i < $allowedStrLen; $i++) {
                $allowed[] = ord($allowedLowChars[$i]);
            }
        }

        $stripped = [];
        if ($stripChars) {
            $stripCharsLen = strlen($stripChars);
            for ($i = 0; $i < $stripCharsLen; $i++) {
                $stripped[] = ord($stripChars[$i]);
            }
        }

        $len = strlen($clean);
        if (!$len) {
            return "";
        }

        $filtered = "";
        for ($i = 0; $i < $len; $i++) {
            $ord = ord($clean[$i]);
            if ($ord < 32) {
                if (!in_array($ord, $allowed)) {
                    continue;
                }
            }

            if (in_array($ord, $stripped)) {
                continue;
            }

            $filtered .= chr($ord);
        }

        return $filtered;
    }

    /**
     * Converts 7-bit ASCII into Hexadecimal encoded string
     * @param string $str
     * @return string
     */
    public static function toHex(string $str): string
    {
        if (!self::Charset($str)) {
            throw new \InvalidArgumentException('Cannot encode UTF-8 string into hexadecimals');
        }

        $hex = "";
        for ($i = 0; $i < strlen($str); $i++) {
            $hex .= str_pad(dechex(ord($str[$i])), 2, "0", STR_PAD_LEFT);
        }

        return $hex;
    }

    /**
     * Decodes hexadecimal encoded string back into 7-bit ASCII characters, also tolerant towards "0x" prefix
     * @param string $hex
     * @return string
     */
    public static function fromHex(string $hex): string
    {
        if (str_starts_with($hex, "0x")) {
            $hex = substr($hex, 0, 2); // If any, removes the "0x" prefix
        }

        if (!preg_match('/^[a-f0-9]+$/i', $hex)) {
            throw new \InvalidArgumentException('Cannot decoded non-hexadecimal value to ASCII');
        }

        if (strlen($hex) % 2 !== 0) {
            $hex = "0" . $hex;
        }

        $str = "";
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return $str;
    }
}
