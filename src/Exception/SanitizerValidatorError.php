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

namespace Charcoal\Charsets\Exception;

/**
 * Class SanitizerValidatorError
 * @package Charcoal\Charsets\Exception
 */
enum SanitizerValidatorError: int
{
    case TYPE_ERROR = 0x64;
    case LENGTH_ERROR = 0xc8;
    case LENGTH_UNDERFLOW_ERROR = 0x12c;
    case LENGTH_OVERFLOW_ERROR = 0x190;
    case REGEXP_MATCH_ERROR = 0x1f4;
    case ENUM_ERROR = 0x258;
    case CALLBACK_TYPE_ERROR = 0x320;
    case CHARSET_ERROR = 0x2bc;
}