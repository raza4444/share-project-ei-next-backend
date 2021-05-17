<?php
/**
 * by stephan scheide
 */

namespace MyTests\Integration;

use App\Utils\DBUtils;
use Illuminate\Support\Facades\DB;

class AbstractIntegrationTest extends \Laravel\Lumen\Testing\TestCase
{

    public const TESTDATABASE = 'einexttest';

    private $testDataService;

    public function setUp()
    {
        parent::setUp();
        $conn = DB::connection();
        $db = $conn->getDatabaseName();
        if ($db != self::TESTDATABASE) {
            $this->fail("Database is $db - not using " . self::TESTDATABASE);
        }

        $this->testDataService = new TestDataService();

        $this->afterSetUp();
    }

    /**
     * @return TestDataService
     */
    public function getTestDataService()
    {
        return $this->testDataService;
    }

    public function tearDown()
    {
        $this->beforeTearDown();
        parent::tearDown();
    }

    public function afterSetUp()
    {
    }

    public function beforeTearDown()
    {
    }

    public function deleteAllOfTable($table)
    {
        DB::table($table)->delete();
    }

    public function assertTableCount($table, $expectedCount)
    {
        $count = DBUtils::quickCount('select count(*) as anzahl from ' . $table);
        $this->assertEquals($expectedCount, $count);
    }

    public function assertTableEmpty($table)
    {
        return $this->assertTableCount($table, 0);
    }

    public function testDatabase()
    {
        $this->assertTableEmpty('users');
    }

    public function login($username, $password)
    {
        $content = $this->post('/rest/public/userlogin', [
            'username' => $username, 'password' => $password
        ])->seeStatusCode(200)->response->getContent();
        $arr = json_decode($content);
        return $arr;
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }


}
