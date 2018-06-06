<?php

namespace gear\helpers;

use gear\library\GHelper;

/**
 * Хелпер для работы с массивами
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class HArray extends GHelper
{
    /**
     * Возвращает true, если указанный массив является ассоциативным
     *
     * @param array $array
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpIsAssoc(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}
