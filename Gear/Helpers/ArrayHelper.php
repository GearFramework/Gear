<?php

namespace gear\helpers;

use Gear\Library\GHelper;

/**
 * Хелпер для работы с массивами
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class ArrayHelper extends GHelper
{
    /**
     * Возвращает true, если указанный массив является ассоциативным
     *
     * @param array $array
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpIsAssoc(array $array): bool
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    public static function helpToString(iterable $array): string
    {
        $string = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::helpToString($value);
            } elseif (is_object($value)) {
                $value = get_class($value);
            } elseif (is_bool($value)) {
                $value = $value ? 'TRUE' : 'FALSE';
            } elseif (is_null($value)) {
                $value = 'NULL';
            } elseif (!is_numeric($value)) {
                $value = '"' . addslashes($value) . '"';
            }
            $string .= "$key: $value, ";
        }
        return "[$string]";
    }
}
