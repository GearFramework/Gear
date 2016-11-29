<?php

use PhpUnit\Framework\TestCase;

class CoreTest extends TestCase
{
    public function addDataProvider()
    {
        return [
            [[]],
        ];
    }

    public function addDataExceptionsProvider()
    {
        return [
            [1],
            [''],
            [
                ['bootstrap' => ['libraries' => ['\gear\library\Test']]],
            ],
            [],
        ];
    }

    public function addDataProviderRegService()
    {
        return [
            ['handlerError', ['class' => '\gear\components\handlers\GErrorsHandlerComponent'], 'component'],
            ['handlerException', ['class' => '\gear\components\handlers\GExceptionsHandlerComponent'], 'component'],
        ];
    }

    public function addDataProviderRegServiceWithException()
    {
        return [
            ['', ['class' => '\gear\components\laoder\GLoaderComponent'], 'component'],
            [function() { return false; }, ['class' => '\gear\components\laoder\GLoaderComponent'], 'component'],
            ['test', [], 'component'],
            ['test', '', 'component'],
            ['test', function() { return false; }, 'component'],
            ['handlerException', ['class' => '\gear\components\handlers\GExceptionsHandlerComponent'], 'component'],
        ];
    }

    /**
     * @dataProvider addDataProvider
     */
    public function testInit($config = [], $mode = \gear\Core::DEVELOPMENT)
    {
        \gear\Core::init($config, $mode);
    }

    /**
     * @dataProvider addDataExceptionsProvider
     * @expectedException Exception
     */
    public function testInitWithException($config = [], $mode = \gear\Core::DEVELOPMENT)
    {
        \gear\Core::init($config, $mode);
        $this->assertEquals(true, in_array('\gear\library\Test', \gear\Core::getConfiguration('bootstrap')['libraries']));
    }

    /**
     * @dataProvider addDataProviderRegService
     */
    public function testRegisterService($name, $service, $type)
    {
        \gear\Core::registerService($name, $service, $type);
        $this->assertEquals(true, \gear\Core::isServiceRegistered($name, $type));
    }

    /**
     * @dataProvider addDataProviderRegServiceWithException
     * @expectedException Exception
     */
    public function testRegisterServiceWithException($name, $service, $type)
    {
        \gear\Core::registerService($name, $service, $type);
    }
}