<?php

use PhpUnit\Framework\TestCase;

class CoreTest extends TestCase
{
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
     * @dataProvider dataProviderResolvePath
     */
    public function testResolvePath($path, $int = false)
    {
        $r = \gear\Core::resolvePath($path, $int);
    }
}