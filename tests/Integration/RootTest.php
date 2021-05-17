<?php

namespace MyTests\Integration;

class RootTest extends \MyTests\Integration\AbstractIntegrationTest
{

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

}
