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

/**
 * Class SanitizerValidatorTests
 */
class SanitizerValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testAsciiProcessorPrintable1(): void
    {
        $str1 = "charcoal\r\nwww.charcoal.de\0v";
        $asciiProcessor = \Charcoal\Charsets\SanitizerValidator::ASCII();
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::CHARSET_ERROR->value);
        $asciiProcessor->getProcessed($str1);
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testAsciiProcessorPrintable2(): void
    {
        $str1 = "charcoal\r\nwww.charcoal.de\0v";
        $asciiProcessor = \Charcoal\Charsets\SanitizerValidator::ASCII();
        $asciiProcessor->printableOnly = false;
        $processed = $asciiProcessor->getProcessed($str1);
        $this->assertEquals($str1, $processed, "Nothing really happened there");
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testInputProcessor1(): void
    {
        $name = "Charcoal  Dev ";
        $nameProcessor = \Charcoal\Charsets\SanitizerValidator::ASCII();
        $nameProcessor->cleanSpaces();
        $this->assertEquals("Charcoal Dev ", $nameProcessor->getProcessed($name), "Clean spaces");
        $nameProcessor->trim();
        $this->assertEquals("Charcoal Dev", $nameProcessor->getProcessed($name), "Trimmed");
        $nameProcessor->upperCase();
        $this->assertEquals("CHARCOAL DEV", $nameProcessor->getProcessed($name), "Uppercase");
        $nameProcessor->lowerCase();
        $this->assertEquals("charcoal dev", $nameProcessor->getProcessed($name), "Lowercase");
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testExactLength(): void
    {
        $processor = \Charcoal\Charsets\SanitizerValidator::ASCII()
            ->len(exact: 8);
        $this->assertEquals("charcoal", $processor->getProcessed("charcoal"));
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::LENGTH_ERROR->value);
        $processor->getProcessed("charcoa");
    }

    public function testNullable(): void
    {
        $processor = \Charcoal\Charsets\SanitizerValidator::ASCII()
            ->len(min: 2, max: 8);
        $this->assertEquals("test", $processor->getNullable("test"));
        $this->assertNull($processor->getNullable(null));
        $this->assertNull($processor->getNullable("", true));
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::TYPE_ERROR->value);
        $processor->getNullable($processor->getProcessed(null));
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testLengthRange1(): void
    {
        $processor = \Charcoal\Charsets\SanitizerValidator::ASCII()
            ->len(min: 2, max: 8);
        $this->assertEquals("charcoal", $processor->getProcessed("charcoal"));
        $this->assertEquals("char", $processor->getProcessed("char"));
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::LENGTH_UNDERFLOW_ERROR->value);
        $processor->getProcessed("c");
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testLengthRange2(): void
    {
        $processor = \Charcoal\Charsets\SanitizerValidator::ASCII()
            ->len(min: 2, max: 8);
        $this->assertEquals("charcoal", $processor->getProcessed("charcoal"));
        $this->assertEquals("char", $processor->getProcessed("char"));
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::LENGTH_OVERFLOW_ERROR->value);
        $processor->getProcessed("charcoal.dev");
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testLengthUTF8(): void
    {
        $processor = \Charcoal\Charsets\SanitizerValidator::UTF8()
            ->addCharset(\Charcoal\Charsets\Utf8Range::Arabic)
            ->len(exact: 6); // match string has exactly 6 characters
        $word = "چارکول";
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        $this->assertEquals(12, strlen($word));
        $this->assertEquals("چارکول", $processor->getProcessed($word));
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::LENGTH_ERROR->value);
        $processor->getProcessed("چارکل");
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testEnum(): void
    {
        $validator = \Charcoal\Charsets\SanitizerValidator::ASCII();
        $validator->enum("yes", "no", "maybe");
        $this->assertEquals("yes", $validator->getProcessed("yes"));
        $this->assertEquals("maybe", $validator->getProcessed("maybe"));
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::ENUM_ERROR->value);
        $validator->getProcessed("what");
    }

    /**
     * @return void
     * @throws \Charcoal\Charsets\Exception\SanitizerValidatorException
     */
    public function testRegExp(): void
    {
        $validator = \Charcoal\Charsets\SanitizerValidator::ASCII();
        $validator->match('/^\w+(\s\w+)*$/');

        $this->assertEquals("Charcoal", $validator->getProcessed("Charcoal"));
        $this->assertEquals("Charcoal Dev", $validator->getProcessed("Charcoal Dev"));
        $this->assertEquals("CharcoalDev", $validator->getProcessed("CharcoalDev"));
        $this->expectExceptionCode(\Charcoal\Charsets\Exception\SanitizerValidatorError::REGEXP_MATCH_ERROR->value);
        $validator->getProcessed("Charcoal #Dev");
    }
}