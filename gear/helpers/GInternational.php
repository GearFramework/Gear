<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GObject;
use gear\interfaces\IFactory;

/**
 * Хелпер интернационализации
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.11.2015
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GInternational extends GObject
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_data = [];
    /* Public */

    /**
     * Перевод текста на текущий язык
     *
     * @access public
     * @static
     * @param string $text
     * @param string $locationLocales
     * @param array $params
     * @return string
     */
    public static function t($text, $locationLocales, $params = [])
    {
        if (!isset(self::$_data[$locationLocales]))
        {
            $locale = Core::params('locale') ?: 'en_En';
            $path = Core::resolvePath($locationLocales . '/' . $locale . '.php');
            if (!file_exists($path))
                throw self::exceptionFileNotFound(['filename' => $path]);
            self::$_data[$locationLocales] = require($path);
        }
        $text = isset(self::$_data[$locationLocales][$text]) ? self::$_data[$locationLocales][$text] : '';
        if ($params)
        {
            foreach($params as $paramName => $paramValue)
                $text = str_replace(':' . $paramName, $paramValue, $text);
        }
        return $text;
    }
}
