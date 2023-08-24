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

use Charcoal\Charsets\SanitizerValidator\ASCII_Processor;
use Charcoal\Charsets\SanitizerValidator\UTF8_Processor;

/**
 * Class SanitizerValidator
 * @package Charcoal\Charsets
 */
class SanitizerValidator
{
    /**
     * @param bool $printableOnly
     * @return \Charcoal\Charsets\SanitizerValidator\ASCII_Processor
     */
    public static function ASCII(bool $printableOnly = true): ASCII_Processor
    {
        return new ASCII_Processor($printableOnly);
    }

    /**
     * @param bool $allowASCII
     * @param bool $allowSpaces
     * @param bool $filterInvalidChars
     * @return \Charcoal\Charsets\SanitizerValidator\UTF8_Processor
     */
    public static function UTF8(
        bool $allowASCII = true,
        bool $allowSpaces = true,
        bool $filterInvalidChars = false
    ): UTF8_Processor
    {
        return new UTF8_Processor($allowASCII, $allowSpaces, $filterInvalidChars);
    }
}
