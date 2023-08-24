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
 * Class Charsets
 * @package Charcoal\Charsets
 */
class Charsets
{
    /**
     * Cleans input string of any leading/trailing spaces,
     * Also replaces multiple-spaces in between string with a single space
     * @param string $input
     * @return string
     */
    public static function cleanSpaces(string $input): string
    {
        return trim(preg_replace('/(\s+)/', ' ', $input));
    }

    /**
     * Checks is input string all comprised of 7-bit ASCII characters
     * @param mixed $val
     * @param bool $printableOnly
     * @return bool
     */
    public static function isASCII(mixed $val, bool $printableOnly = true): bool
    {
        if (!is_string($val)) {
            return false;
        }

        return $printableOnly ? ASCII::isPrintableOnly($val) : ASCII::Charset($val);
    }

    /**
     * Checks if string may have UTF8 characters
     * @param mixed $val
     * @return bool
     */
    public static function hasUtf8Chars(mixed $val): bool
    {
        return is_string($val) && !ASCII::Charset($val);
    }

    /**
     * Checks if input is a string and is Hexadecimal encoded
     * @param mixed $val
     * @return bool
     */
    public static function isBase16(mixed $val): bool
    {
        return is_string($val) && preg_match('/^(0x)?[a-f0-9]+$/i', $val);
    }
}
