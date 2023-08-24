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
 * Class UTF8
 * @package Charcoal\Charsets
 */
class UTF8
{
    /**
     * Verifies whether the input string falls within a UTF8 charset range
     * and if it can include spaces and 7-bit ASCII characters
     * @param string $input
     * @param bool $spaces
     * @param bool $ascii
     * @param \Charcoal\Charsets\Utf8Range ...$charsets
     * @return bool
     */
    public static function Check(string $input, bool $spaces = true, bool $ascii = true, Utf8Range ...$charsets): bool
    {
        $ranges = "";
        foreach ($charsets as $charset) {
            $ranges .= $charset->unicodeRange();
        }

        $spaces = $spaces ? '\s' : '';
        $ascii = $ascii ? "\x20-\x7E" : "";
        $exp = '/^[' . $spaces . $ascii . $ranges . ']+$/u';
        return (bool)preg_match($exp, $input);
    }

    /**
     * Filters out any character that is not in specified UTF8 character range,
     * optionally keeps printable 7-bit ASCII characters (dec range 32-127)
     * @param string $input
     * @param bool $ascii
     * @param \Charcoal\Charsets\Utf8Range ...$charsets
     * @return string
     */
    public static function Filter(string $input, bool $ascii = true, Utf8Range ...$charsets): string
    {
        $ranges = "";
        foreach ($charsets as $charset) {
            $ranges .= $charset->unicodeRange();
        }

        $ascii = $ascii ? "\x20-\x7E" : "";
        $exp = "/[^" . $ascii . $ranges . "]+/u";
        return preg_replace($exp, "", $input);
    }
}