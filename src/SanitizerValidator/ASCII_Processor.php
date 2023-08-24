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

namespace Charcoal\Charsets\SanitizerValidator;

use Charcoal\Charsets\Exception\SanitizerValidatorError;
use Charcoal\Charsets\Exception\SanitizerValidatorException;

/**
 * Class ASCII_Processor
 * @package Charcoal\Charsets\InputProcessor
 */
class ASCII_Processor extends AbstractSanitizerValidator
{
    /**
     * @param bool $printableOnly
     */
    public function __construct(
        public bool $printableOnly = true,
    )
    {
    }

    /**
     * @param string $value
     * @return string
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    protected function charsetCallback(string $value): string
    {
        $pattern = $this->printableOnly ? '/^[\x20-\x7E]*$/' : '/^[\x00-\x7F]*$/';
        if (!preg_match($pattern, $value)) {
            throw new SanitizerValidatorException(SanitizerValidatorError::CHARSET_ERROR);
        }

        return $value;
    }

    /**
     * @param string $in
     * @return string
     */
    protected function toUpperCase(string $in): string
    {
        return strtoupper($in);
    }

    /**
     * @param string $in
     * @return string
     */
    protected function toLowerCase(string $in): string
    {
        return strtolower($in);
    }

    /**
     * @param string $in
     * @return int
     */
    protected function getLength(string $in): int
    {
        return strlen($in);
    }
}
