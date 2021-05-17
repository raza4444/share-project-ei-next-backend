<?php
/**
 * by stephan scheide
 */

namespace App\Services\Ssl;

use App\MyTestCase;

class SslServiceTest extends MyTestCase
{

    public function testIsDomainValid()
    {
        //self::assertFalse(SslService::isDomainValid('xn--kingster.de'));
        self::assertTrue(SslService::isDomainValid('kingster.de'));
        self::assertFalse(SslService::isDomainValid('www.kingster.de'));
        self::assertFalse(SslService::isDomainValid('http://kingster.de'));
        self::assertFalse(SslService::isDomainValid('kingster.de/de'));
        self::assertFalse(SslService::isDomainValid('www.r2handwerk.de/de'));
    }

    public function testrepairDomainOnly()
    {
        self::assertEquals("me.de", SslService::repairDomainOnly("me.de"));
        self::assertEquals("me.de", SslService::repairDomainOnly("www.me.de"));
        self::assertEquals("me.de", SslService::repairDomainOnly(" www.me.de "));
        self::assertEquals("me.de", SslService::repairDomainOnly("http://www.me.de "));
        self::assertEquals("me.de", SslService::repairDomainOnly("http://www.me.de/ "));
    }
}
