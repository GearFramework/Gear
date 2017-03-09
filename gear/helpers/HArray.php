<?php

namespace gear\helpers;

use gear\library\GHelper;

class HArray extends GHelper
{
    public static function helpIsAssoc($array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}
