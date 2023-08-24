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
 * Class SanitizerValidatorException
 * @package Charcoal\Charsets\Exception
 */
class SanitizerValidatorException extends \Exception
{
    /**
     * @param \Charcoal\Charsets\Exception\SanitizerValidatorError $error
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(
        public readonly SanitizerValidatorError $error,
        string                                  $message = "",
        ?\Throwable                             $previous = null
    )
    {
        parent::__construct($message, $this->error->value, $previous);
    }
}