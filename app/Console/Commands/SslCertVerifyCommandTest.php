<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use PHPUnit\Framework\TestCase;

class SslCertVerifyCommandTest extends TestCase
{

    public function testExtractOutputData()
    {
        $data = SslCertVerifyCommand::extractOutputData("notBefore=Feb 13 00:00:00 2020 GMT\nnotAfter=Feb 21 23:59:59 2022 GMT\nissuer=C = GB, ST = Greater Manchester, L = Salford, O = Sectigo Limited, CN = Sectigo RSA Domain Validation Secure Server CA)\n");
        self::assertNotNull($data);
        self::assertEquals(0, $data[0]);
        self::assertEquals("2022-02-21",$data[3]);

        $data = SslCertVerifyCommand::extractOutputData("notBefore=Feb 13 00:00:00 2020 GMT\nnotAfter=Feb 21 23:59:59 2022 GMT\nissuer=C = GB, ST = Greater Manchester, L = Salford, O = Sectigo Limited, CN = let's encrypt auth)\n");
        self::assertNotNull($data);
        self::assertEquals(1, $data[0]);
        self::assertEquals("2022-02-21",$data[3]);

        $data = SslCertVerifyCommand::extractOutputData("notBefore=Feb 13 00:00:00 2020 GMT\nnotAfter=Feb 21 23:59:59 2020 GMT\nissuer=C = GB, ST = Greater Manchester, L = Salford, O = Sectigo Limited, CN = let's encrypt auth)\n");
        self::assertNotNull($data);
        self::assertEquals(0, $data[0]);
        self::assertEquals("2020-02-21",$data[3]);

    }

}
