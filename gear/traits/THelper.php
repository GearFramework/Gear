<?php

namespace gear\traits;

trait THelper
{
    public function __call(string $name, array $arguments)
    {
        $helper = 'help' . ucfirst($name);
        if (method_exists($this, $helper)) {
            return $this->$helper(...$arguments);
        }
    }
}