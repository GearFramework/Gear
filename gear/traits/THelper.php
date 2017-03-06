<?php

namespace gear\traits;

trait THelper
{
    public static function __callStatic(string $name, array $arguments)
    {
        $helper = 'help' . ucfirst($name);
        if (method_exists(static::class, $helper)) {
            return static::$helper(...$arguments);
        }
    }

    public function __call(string $name, array $arguments)
    {
        $helper = 'help' . ucfirst($name);
        if (method_exists(static::class, $helper)) {
            return static::$helper(...$arguments);
        }
    }
}