<?php

use PhpUnit\Framework\TestCase;

class CoreTest extends TestCase
{
    public function dataProviderConfigure()
    {
        return [
            [[
                'class' => '\ClassName',
                'property1' => 'value',
            ], [
                '\ClassName',
                [],
                ['property1' => 'value'],
            ]],
            // -----------
            [[
                'property1' => 'value',
            ], [
                null,
                [],
                ['property1' => 'value'],
            ]],
            // -----------
            [[
                'class' => [
                    'name' => '\ClassName',
                    'staticProperty' => 'value',
                ],
                'property1' => 'value',
            ], [
                '\ClassName',
                ['staticProperty' => 'value'],
                ['property1' => 'value'],
            ]],
            // -------------
            [[
                'class' => [
                    'staticProperty' => 'value',
                ],
                'property1' => 'value',
            ], [
                null,
                ['staticProperty' => 'value'],
                ['property1' => 'value'],
            ]],
            // -------------
            [[
                'class' => '\ClassName',
            ], [
                '\ClassName',
                [],
                [],
            ]],
            // -------------
            [[
            ], [
                null,
                [],
                [],
            ]],
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

    /**
     * @dataProvider dataProviderConfigure
     */
    public function testConfigure(array $config, array $result)
    {
        $r = \gear\Core::configure($config);
        $this->assertEquals(0, strcmp(serialize($r), serialize($result)));
    }

    /**
     * @dataProvider dataProviderResolvePath
     */
    public function testResolvePath($path, $int = false)
    {
        $r = \gear\Core::resolvePath($path, $int);
    }
}