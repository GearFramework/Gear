<?php

use PhpUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function setUp()
    {
        \gear\Core::init(['modules' => [
            'app' => ['class' => '\demo\hello\Hello'],
        ]]);
        \gear\Core::installService('errorsHandler', ['class' => '\gear\components\handlers\GErrorsHandlerComponent'], 'component');
        \gear\Core::installService('exceptionsHandler', ['class' => '\gear\components\handlers\GExceptionsHandlerComponent'], 'component');
        \gear\Core::installService('loader', ['class' => '\gear\components\loader\GLoaderComponent'], 'component');
    }

    public function testApplicationRun()
    {
        $this->assertEquals(true, \gear\Core::isServiceRegistered('app', 'module'));
        $app = \gear\Core::app();
        $this->assertEquals(true, $app instanceof \gear\interfaces\IModule);
        $app->run();
    }
}