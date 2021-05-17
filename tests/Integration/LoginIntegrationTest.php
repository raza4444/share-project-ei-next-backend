<?php

namespace MyTests\Integration;


use Tests\AbstractIntegrationTest;

class LoginIntegrationTest extends \MyTests\Integration\AbstractIntegrationTest
{

    public function afterSetUp()
    {
    }

    public function beforeTearDown()
    {
        $this->deleteAllOfTable('users');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoot()
    {
        $this->get('/');
        $this->assertTrue(strpos($this->response->getContent(), $this->app->version()) === 0);
    }

    public function testLoginNoUser()
    {
        $this->assertTableEmpty('users');
        $this->post('/rest/public/userlogin',
            ['username' => 'unknown', 'password' => 848484])->seeStatusCode(403);
    }

    public function testLoginAdmin()
    {
        $user = new \App\Entities\Core\InternUser();
        $user->username = 'admin';
        $user->password = md5('test');
        $user->admin = 1;
        $user->save();

        $this->post('/rest/public/userlogin',
            ['username' => 'admin', 'password' => 'test'])->seeStatusCode(200);

        $this->post('/rest/public/userlogin',
            ['username' => 'admin', 'password' => 'test1'])->seeStatusCode(403);

        $this->post('/rest/public/userlogin',
            ['username' => 'admin', 'password' => '848484'])->seeStatusCode(200);

    }

    public function testLoginNormalUser()
    {

        $this->getTestDataService()->createNormalUser('judas', 'test');

        $this->post('/rest/public/userlogin',
            ['username' => 'judas', 'password' => 'test'])->seeStatusCode(200);
    }

}
