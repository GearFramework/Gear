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
            ['errorsHandler', ['class' => '\gear\components\handlers\GErrorsHandlerComponent'], 'component'],
            ['exceptionsHandler', ['class' => '\gear\components\handlers\GExceptionsHandlerComponent'], 'component'],
            ['loader', ['class' => '\gear\components\loader\GLoaderComponent'], 'component'],
            ['test', ['class' => '\gear\components\GTestComponent'], 'component'],
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
            ['exceptionsHandler', ['class' => '\gear\components\handlers\GExceptionsHandlerComponent'], 'component'],
        ];
    }

    public function dataProviderGetRegisteredService()
    {
        return [
            ['errorsHandler'],
            ['exceptionsHandler'],
            ['loader'],
            ['errorsHandler', 'component'],
            ['exceptionsHandler', 'component'],
            ['loader', 'component'],
        ];
    }

    public function addDataProviderInstallService()
    {
        return [
            ['errorsHandler', 'component'],
            ['exceptionsHandler', 'component'],
            ['loader', 'component'],
            ['app', ['class' => '\demo\hello\Hello'], 'module'],
        ];
    }

    public function dataProviderResolvePath()
    {
        return [
            [''],
            ['', true],
            ['/usr'],
            ['/usr', true],
            ['\gear'],
            ['\gear', true],
            ['library'],
            ['library', true],
            ['/usr/lib'],
            ['/usr/lib', true],
            ['\gear\library'],
            ['\gear\library', true],
            ['\demo\hello'],
            ['\demo\hello', true],
        ];
    }

    public function dataProviderUnregisteredService()
    {
        return [
            ['test', 'component'],
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

    /**
     * @dataProvider dataProviderGetRegisteredService
     */
    public function testGetRegisteredService($name, $type = null)
    {
        $s = \gear\Core::getRegisteredService($name, $type);
        $this->assertEquals(true, is_array($s) && !empty($s));
    }

    /**
     * @dataProvider addDataProviderInstallService
     * @depends testRegisterService
     */
    public function testInstallService($name, $type = null, $owner = null)
    {
        if (!is_array($type))
            $service = \gear\Core::getRegisteredService($name, $type);
        else {
            $service = $type;
            $type = $owner;
            if (func_num_args() > 3)
                $owner = func_get_arg(3);
            else
                $owner = null;
        }
        \gear\Core::installService($name, $service, $type, $owner);
        $this->assertEquals(true, \gear\Core::isServiceInstalled($name, $type));
    }

    /**
     * @dataProvider dataProviderResolvePath
     */
    public function testResolvePath($path, $int = false)
    {
        $r = \gear\Core::resolvePath($path, $int);
    }

    /**
     * @dataProvider dataProviderUnregisteredService
     */
    public function testUnregisteredService($name, $type)
    {
        \gear\Core::unregisterService($name, $type);
        $this->assertEquals(false, \gear\Core::isServiceRegistered($name, $type));
    }
}