<?php

use PhpUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testApplicationRun()
    {
        $this->assertEquals(true, \gear\Core::isServiceRegistered('app', 'module'));
        $app = \gear\Core::app();
        $this->assertEquals(true, $app instanceof \gear\interfaces\IModule);
        $app->run();
    }
}