<?php

namespace gear\traits;

trait TNamedService
{
    public function getNameService($section = null)
    {
        return self::class . '.' . ($section ? $section : '');
    }
}