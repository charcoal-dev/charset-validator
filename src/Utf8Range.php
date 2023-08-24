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
 * Class Utf8Range
 * @package Charcoal\Charsets
 */
enum Utf8Range
{
    case Arabic;
    case Hebrew;
    case Russian;
    case Thai;

    /**
     * @return string
     */
    public function unicodeRange(): string
    {
        return match ($this) {
            self::Hebrew => "\x{591}-\x{5C7}\x{5D0}-\x{5EA}\x{5EF}-\x{5F4}",
            self::Arabic => "\x{600}-\x{604}\x{606}-\x{60B}\x{60D}-\x{61A}\x{61E}\x{620}-\x{63F}\x{641}-\x{64A}\x{656}-\x{66F}\x{671}-\x{6DC}\x{6DE}-\x{6FF}\x{750}-\x{77F}\x{8A0}-\x{8B4}\x{8B6}-\x{8BD}\x{8D4}-\x{8E1}\x{8E3}-\x{8FF}\x{FB1D}-\x{FB36}\x{FB38}-\x{FB3C}\x{FB3E}\x{FB40}-\x{FB41}\x{FB43}-\x{FB44}\x{FB46}-\x{FBC1}\x{FBD3}-\x{FD3D}\x{FD50}-\x{FD8F}\x{FD92}-\x{FDC7}\x{FDF0}-\x{FDFD}\x{FE70}-\x{FE74}\x{FE76}-\x{FEFC}\x{10E60}-\x{10E7E}\x{1EE00}-\x{1EE03}\x{1EE05}-\x{1EE1F}\x{1EE21}-\x{1EE22}\x{1EE24}\x{1EE27}\x{1EE29}-\x{1EE32}\x{1EE34}-\x{1EE37}\x{1EE39}\x{1EE3B}\x{1EE42}\x{1EE47}\x{1EE49}\x{1EE4B}\x{1EE4D}-\x{1EE4F}\x{1EE51}-\x{1EE52}\x{1EE54}\x{1EE57}\x{1EE59}\x{1EE5B}\x{1EE5D}\x{1EE5F}\x{1EE61}-\x{1EE62}\x{1EE64}\x{1EE67}-\x{1EE6A}\x{1EE6C}-\x{1EE72}\x{1EE74}-\x{1EE77}\x{1EE79}-\x{1EE7C}\x{1EE7E}\x{1EE80}-\x{1EE89}\x{1EE8B}-\x{1EE9B}\x{1EEA1}-\x{1EEA3}\x{1EEA5}-\x{1EEA9}\x{1EEAB}-\x{1EEBB}\x{1EEF0}-\x{1EEF1}",
            self::Russian => "\x{400}-\x{487}\x{48A}-\x{52F}",
            self::Thai => "\x{E01}-\x{E3A}\x{E3F}-\x{E5B}",
        };
    }
}