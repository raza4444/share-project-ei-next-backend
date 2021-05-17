<?php
/**
 * by stephan scheide
 */

namespace App\Utils;

use App\MyTestCase;

class StringUtilsTest extends MyTestCase
{

    public function testcreateGUID()
    {
        $this->assertEquals(36, strlen(StringUtils::createGUID()));
    }

    public function testIsEmpty()
    {
        $this->assertTrue(StringUtils::isEmpty(null));
        $this->assertTrue(StringUtils::isEmpty(" "));
        $this->assertTrue(StringUtils::isEmpty(""));
        $this->assertFalse(StringUtils::isEmpty("Hallo"));
    }

    public function testuseNonEmpty()
    {
        $this->assertEquals('Ich', StringUtils::useNonEmpty(null, 'Ich'));
        $this->assertEquals('Ich', StringUtils::useNonEmpty(null, ' ', '', 'Ich'));
    }

    public function testisTooShort()
    {
        $this->assertTrue(StringUtils::isTooShort(null, 3));
        $this->assertTrue(StringUtils::isTooShort(' ', 3));
        $this->assertTrue(StringUtils::isTooShort(' ab', 3));
        $this->assertFalse(StringUtils::isTooShort('dab', 3));
    }

    public function testensureInteger()
    {
        $this->assertEquals(0, StringUtils::ensureInteger('123a'));
        $this->assertEquals(15, StringUtils::ensureInteger('15'));
    }

    public function testonlyFiguresAndNumbers()
    {
        self::assertEquals("hans", StringUtils::onlyFiguresAndNumbers("hans"));
        self::assertEquals("wwwr2handwerkdede", StringUtils::onlyFiguresAndNumbers("www.r2handwerk.de/de"));
    }


}
