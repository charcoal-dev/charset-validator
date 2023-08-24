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
 * Class AbstractSanitizerValidator
 * @package Charcoal\Charsets\Sanitizer
 */
abstract class AbstractSanitizerValidator
{
    /** @var int */
    protected const CHANGE_CASE_LC = 0x01;
    /** @var int */
    protected const CHANGE_CASE_UC = 0x02;
    /** @var int */
    protected const TRIM_BOTH = 0x0a;
    /** @var int */
    protected const TRIM_LEFT = 0x0b;
    /** @var int */
    protected const TRIM_RIGHT = 0x0c;

    /** @var array|null */
    protected ?array $enum = null;
    /** @var null|int */
    protected ?int $exactLen = null;
    /** @var int|null */
    protected ?int $minLen = null;
    /** @var int|null */
    protected ?int $maxLen = null;
    /** @var string|null */
    protected ?string $matchExp = null;
    /** @var int|null */
    protected ?int $changeCase = null;
    /** @var int|null */
    protected ?int $trim = null;
    /** @var bool Clean multiple spaces with a single space */
    protected bool $cleanSpaces = false;
    /** @var string */
    protected string $trimChars = " \n\r\t\v\0";
    /** @var \Closure|null */
    protected ?\Closure $callbackFn = null;

    /**
     * Validates length of input strings between specified minimum, maximum or exact match
     * (multibyte extension is used for UTF8 validations)
     * @param int|null $exact
     * @param int|null $min
     * @param int|null $max
     * @return $this
     */
    public function len(?int $exact = null, ?int $min = null, ?int $max = null): static
    {
        if ($exact > 0) {
            $this->exactLen = $exact;
            $this->minLen = null;
            $this->maxLen = null;
            return $this;
        }

        $this->exactLen = null;
        $this->minLen = $min > 0 ? $min : null;
        $this->maxLen = $max > 0 ? $max : null;
        return $this;
    }

    /**
     * Invokes trim function on value
     * @return $this
     */
    public function trim(string $chars = " \n\r\t\v\0"): static
    {
        $this->trim = self::TRIM_BOTH;
        $this->trimChars = $chars;
        return $this;
    }

    /**
     * Invokes ltrim function on value
     * @param string $chars
     * @return $this
     */
    public function ltrim(string $chars = " \n\r\t\v\0"): static
    {
        $this->trim = self::TRIM_LEFT;
        $this->trimChars = $chars;
        return $this;
    }

    /**
     * Invokes rtrim function on value
     * @param string $chars
     * @return $this
     */
    public function rtrim(string $chars = " \n\r\t\v\0"): static
    {
        $this->trim = self::TRIM_RIGHT;
        $this->trimChars = $chars;
        return $this;
    }

    /**
     * Matches value with RegExp
     * @param string $regExp
     * @return $this
     */
    public function match(string $regExp): static
    {
        $this->matchExp = $regExp;
        return $this;
    }

    /**
     * Checks if value is in Array<string>
     * @param string ...$opts
     * @return $this
     */
    public function enum(string ...$opts): static
    {
        $this->enum = $opts;
        return $this;
    }

    /**
     * Changes value to lowercase (uses multibyte extension for UTF8)
     * @return $this
     */
    public function lowerCase(): static
    {
        $this->changeCase = self::CHANGE_CASE_LC;
        return $this;
    }

    /**
     * Changes value to lowercase (uses multibyte extension for UTF8)
     * @return $this
     */
    public function upperCase(): static
    {
        $this->changeCase = self::CHANGE_CASE_UC;
        return $this;
    }

    /**
     * Replace any multiple spaces in-between of string with single
     * @return $this
     */
    public function cleanSpaces(): static
    {
        $this->cleanSpaces = true;
        return $this;
    }

    /**
     * @param mixed $value
     * @param bool $emptyStrIsNull
     * @return string|null
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function getNullable(mixed $value, bool $emptyStrIsNull = false): ?string
    {
        if (is_null($value) || ($emptyStrIsNull && is_string($value) && !$value)) {
            return null;
        }

        return $this->getProcessed($value);
    }

    /**
     * @param mixed $value
     * @return string
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function getProcessed(mixed $value): string
    {
        // Type
        if (!is_string($value)) {
            throw new SanitizerValidatorException(SanitizerValidatorError::TYPE_ERROR);
        }

        // Trim values?
        if ($this->trim) {
            $value = match ($this->trim) {
                self::TRIM_RIGHT => rtrim($value, $this->trimChars),
                self::TRIM_LEFT => ltrim($value, $this->trimChars),
                default => trim($value, $this->trimChars)
            };
        }

        // Clean multi-spaces and tabs with a single space
        if ($this->cleanSpaces) {
            $value = preg_replace('/(\s+)/', " ", $value);
        }

        // Change Case
        if ($this->changeCase) {
            $value = match ($this->changeCase) {
                self::CHANGE_CASE_UC => $this->toUpperCase($value),
                default => $this->toLowerCase($value)
            };
        }

        // Charset-spec sanitizations
        $value = $this->charsetCallback($value);

        // Check length
        $valueLen = $this->getLength($value);
        if ($this->exactLen) {
            if ($valueLen !== $this->exactLen) {
                throw new SanitizerValidatorException(SanitizerValidatorError::LENGTH_ERROR);
            }
        } elseif ($this->minLen || $this->maxLen) {
            if ($this->minLen && $valueLen < $this->minLen) {
                throw new SanitizerValidatorException(SanitizerValidatorError::LENGTH_UNDERFLOW_ERROR);
            }

            if ($this->maxLen && $valueLen > $this->maxLen) {
                throw new SanitizerValidatorException(SanitizerValidatorError::LENGTH_OVERFLOW_ERROR);
            }
        }

        // RegExp match
        if ($this->matchExp && !preg_match($this->matchExp, $value)) {
            throw new SanitizerValidatorException(SanitizerValidatorError::REGEXP_MATCH_ERROR);
        }

        // Check if is in defined Array
        if ($this->enum) {
            if (!in_array($value, $this->enum)) {
                throw new SanitizerValidatorException(SanitizerValidatorError::ENUM_ERROR);
            }
        }

        // Custom validator
        if ($this->callbackFn) {
            $value = call_user_func($this->callbackFn, $value);
            if (!is_string($value)) {
                throw new SanitizerValidatorException(SanitizerValidatorError::CALLBACK_TYPE_ERROR);
            }
        }

        return $value;
    }

    /**
     * Define a custom validation or sanitization in callback
     * @param \Closure $customValidatorFn
     * @return $this
     */
    public function setCustomFn(\Closure $customValidatorFn): static
    {
        $this->callbackFn = $customValidatorFn;
        return $this;
    }

    /**
     * Clears-out custom defined validation/sanitization callback method
     * @return $this
     */
    public function clearCustomFn(): static
    {
        $this->callbackFn = null;
        return $this;
    }

    /**
     * @param string $value
     * @return string
     */
    abstract protected function charsetCallback(string $value): string;

    /**
     * @param string $in
     * @return string
     */
    abstract protected function toLowerCase(string $in): string;

    /**
     * @param string $in
     * @return string
     */
    abstract protected function toUpperCase(string $in): string;

    /**
     * @param string $in
     * @return int
     */
    abstract protected function getLength(string $in): int;
}

