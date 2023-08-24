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
 * Class CharsetsTest
 */
class CharsetsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testCleanSpaces(): void
    {
        $this->assertEquals("charcoal rocks", \Charcoal\Charsets\Charsets::cleanSpaces("charcoal         rocks"));
        $this->assertEquals("charcoal rocks", \Charcoal\Charsets\Charsets::cleanSpaces("charcoal  rocks "));
        $this->assertEquals("charcoal rocks", \Charcoal\Charsets\Charsets::cleanSpaces(" charcoal rocks"));
        $this->assertEquals("چارکول فريمورک", \Charcoal\Charsets\Charsets::cleanSpaces("چارکول       فريمورک "), "UTF-8 string test");
    }

    /**
     * @return void
     */
    public function testAscii(): void
    {
        $this->assertFalse(\Charcoal\Charsets\Charsets::isASCII(null));
        $this->assertFalse(\Charcoal\Charsets\Charsets::isASCII(1));
        $this->assertTrue(\Charcoal\Charsets\Charsets::isASCII("charcoal", true));

        // Printable tests
        $this->assertTrue(\Charcoal\Charsets\Charsets::isASCII("charcoal\0dev", false), "Check entire 7-bit ASCII set");
        $this->assertFalse(\Charcoal\Charsets\Charsets::isASCII("charcoal\0dev", true), "Check only printable ASCII set");

        // ASCII table 32 to 127
        $this->assertTrue(\Charcoal\Charsets\Charsets::isASCII(
            " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~",
            printableOnly: true
        ));
        $this->assertTrue(\Charcoal\Charsets\Charsets::isASCII(
            " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~",
            printableOnly: false
        ));

        $this->assertTrue(\Charcoal\Charsets\ASCII::isPrintableOnly(
            " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~"
        ));
        $this->assertTrue(\Charcoal\Charsets\ASCII::Charset(
            " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~"
        ));

        // Entire ASCII table (0 to 127)
        $asciiCharset = "";
        for ($i = 0; $i <= 127; $i++) {
            $asciiCharset .= chr($i);
        }

        $this->assertFalse(\Charcoal\Charsets\ASCII::isPrintableOnly($asciiCharset));
        $this->assertTrue(\Charcoal\Charsets\ASCII::Charset($asciiCharset));

        $this->assertTrue(\Charcoal\Charsets\Charsets::isASCII($asciiCharset, printableOnly: false));
        $this->assertFalse(\Charcoal\Charsets\Charsets::isASCII($asciiCharset, printableOnly: true));
    }

    /**
     * @return void
     */
    public function testHasUtf8Chars(): void
    {
        $this->assertTrue(\Charcoal\Charsets\Charsets::hasUtf8Chars(chr(128)));
        $this->assertTrue(\Charcoal\Charsets\Charsets::hasUtf8Chars("چارکول"));
        $this->assertTrue(\Charcoal\Charsets\Charsets::hasUtf8Chars("چارکول charcoal"));

        $this->assertFalse(\Charcoal\Charsets\Charsets::hasUtf8Chars("charcoal"));
        $this->assertFalse(\Charcoal\Charsets\Charsets::hasUtf8Chars("\0charcoal\r\n"));
        $this->assertFalse(\Charcoal\Charsets\Charsets::hasUtf8Chars(chr(32) . chr(127)));
    }

    /**
     * @return void
     */
    public function testBase16Hex(): void
    {
        $this->assertTrue(\Charcoal\Charsets\Charsets::isBase16("0xabcdef1234567890"), "Tolerates 0x prefix");
        $this->assertTrue(\Charcoal\Charsets\Charsets::isBase16("123456789"));
        $this->assertTrue(\Charcoal\Charsets\Charsets::isBase16("a1b2c3d4f5"));
        $this->assertFalse(\Charcoal\Charsets\Charsets::isBase16(" a1b2c3"), "Does not perform any sanitization");
        $this->assertFalse(\Charcoal\Charsets\Charsets::isBase16("a1b2c3 "), "Does not perform any sanitization");

        $this->assertEquals("charcoal", \Charcoal\Charsets\ASCII::fromHex("63686172636F616C"));
        $this->assertEquals("firstByte", \Charcoal\Charsets\ASCII::fromHex("0x666972737442797465"), "Prefix test");
        $this->assertEquals("466972737442797465", \Charcoal\Charsets\ASCII::toHex("FirstByte"));
    }

    /**
     * @return void
     */
    public function testAsciiFilter(): void
    {
        $str = chr(128) . " charcoal  \0dev " . chr(180) . chr(155);
        $this->assertEquals(" charcoal  dev ", \Charcoal\Charsets\ASCII::Filter($str));
        $this->assertEquals(" charcoal  \0dev ", \Charcoal\Charsets\ASCII::Filter($str, allowedLowChars: "\0"));
        $this->assertEquals(" chrcol  \0dev ", \Charcoal\Charsets\ASCII::Filter($str, allowedLowChars: "\0\r\n", stripChars: "a"));
        $this->assertEquals("charcoaldev", \Charcoal\Charsets\ASCII::Filter($str, stripChars: " "));
        $this->assertEquals("chrcoldv", \Charcoal\Charsets\ASCII::Filter($str, stripChars: "ea "));
    }

    /**
     * @return void
     */
    public function testUtf8Range(): void
    {
        $urdu = "چارکول";
        $this->assertFalse(\Charcoal\Charsets\UTF8::Check($urdu));
        $this->assertTrue(\Charcoal\Charsets\UTF8::Check($urdu, charsets: \Charcoal\Charsets\Utf8Range::Arabic));
        $name = "charcoal " . $urdu;
        $this->assertFalse(\Charcoal\Charsets\UTF8::Check($name, ascii: false, charsets: \Charcoal\Charsets\Utf8Range::Arabic));
        $this->assertTrue(\Charcoal\Charsets\UTF8::Check($name, ascii: true, charsets: \Charcoal\Charsets\Utf8Range::Arabic));
        $this->assertFalse(\Charcoal\Charsets\UTF8::Check($name, false, false, \Charcoal\Charsets\Utf8Range::Arabic));

        // This works because even if spaces is set to false, space character is included in ASCII charset:
        $this->assertTrue(\Charcoal\Charsets\UTF8::Check($name, false, true, \Charcoal\Charsets\Utf8Range::Arabic));

        // "charcoal" asserts to FALSE when "ascii" is false
        $this->assertFalse(\Charcoal\Charsets\UTF8::Check("charcoal", ascii: false, charsets: \Charcoal\Charsets\Utf8Range::Arabic));
        $this->assertTrue(\Charcoal\Charsets\UTF8::Check("charcoal", ascii: true, charsets: \Charcoal\Charsets\Utf8Range::Arabic));
    }

    /**
     * @return void
     */
    public function testUtf8Filter(): void
    {
        $str = "\0charcoal уголь چارکول";
        $this->assertEquals("charcoal  چارکول", \Charcoal\Charsets\UTF8::Filter($str, true, \Charcoal\Charsets\Utf8Range::Arabic));
        $this->assertEquals("چارکول", \Charcoal\Charsets\UTF8::Filter($str, false, \Charcoal\Charsets\Utf8Range::Arabic));

        // Multiple ranges
        $rangesSet1 = [
            \Charcoal\Charsets\Utf8Range::Arabic,
            \Charcoal\Charsets\Utf8Range::Russian
        ];
        $this->assertEquals("угольچارکول", \Charcoal\Charsets\UTF8::Filter($str, false, ...$rangesSet1));
        $this->assertEquals("charcoal уголь چارکول", \Charcoal\Charsets\UTF8::Filter($str, true, ...$rangesSet1));

        // Test ASCII::Filter
        // Notice 2 trailing spaces:
        $this->assertEquals("charcoal  ", \Charcoal\Charsets\ASCII::Filter($str));
    }
}

