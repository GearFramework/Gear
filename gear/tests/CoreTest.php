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

    public function testRegisterService($name, $service, $type)
    {
        \gear\Core::registerService($name, $service, $type);
        $this->assertEquals(true, \gear\Core::isServiceRegistered($name, $type));
    }
}