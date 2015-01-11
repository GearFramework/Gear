<?php

namespace gear\traits;

trait TFactory
{
    public function factory(array $properties = [])
    {
        $properties = array_merge(static::$_factoryItem, $properties);
        list($class, $config, $properties) = Core::getRecords($properties);
        if (method_exists($class, 'init'))
            $class::init($config);
        $properties['owner'] = $this;
        return method_exists($class, 'it') ? $class::it($properties) : new $class($properties);
    }
}