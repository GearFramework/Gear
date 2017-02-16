<?php

namespace gear\helpers;

class HArray extends GHelper
{
    public static function helpIsAssoc($array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}
