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
use Charcoal\Charsets\UTF8;
use Charcoal\Charsets\Utf8Range;

/**
 * Class UTF8_Processor
 * @package Charcoal\Charsets\SanitizerValidator
 */
class UTF8_Processor extends AbstractSanitizerValidator
{
    private array $utf8Ranges = [];

    /**
     * @param bool $allowASCII
     * @param bool $allowSpaces
     * @param bool $filterInvalidChars
     */
    public function __construct(
        public bool $allowASCII = true,
        public bool $allowSpaces = true,
        public bool $filterInvalidChars = false,
    )
    {
    }

    /**
     * @param \Charcoal\Charsets\Utf8Range $charsetRange
     * @return $this
     */
    public function addCharset(Utf8Range $charsetRange): static
    {
        $this->utf8Ranges[] = $charsetRange;
        return $this;
    }

    /**
     * @param string $value
     * @return string
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    protected function charsetCallback(string $value): string
    {
        if ($this->filterInvalidChars) {
            $value = UTF8::Filter($value, $this->allowASCII, ...$this->utf8Ranges);
        }

        if (!UTF8::Check($value, $this->allowSpaces, $this->allowASCII, ...$this->utf8Ranges)) {
            throw new SanitizerValidatorException(SanitizerValidatorError::CHARSET_ERROR);
        }

        return $value;
    }

    /**
     * @param string $in
     * @return string
     */
    protected function toLowerCase(string $in): string
    {
        return mb_strtolower($in, "UTF-8");
    }

    /**
     * @param string $in
     * @return string
     */
    protected function toUpperCase(string $in): string
    {
        return mb_strtoupper($in, "UTF-8");
    }

    /**
     * @param string $in
     * @return int
     */
    protected function getLength(string $in): int
    {
        return mb_strlen($in, "UTF-8");
    }
}
