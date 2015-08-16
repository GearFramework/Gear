<?php

namespace gear\traits;
use gear\Core;

/**
 * Трейт обработки исключений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 15.08.2015
 * @php 5.3.x
 */
trait TException
{
    /**
     * Генерация вызванного статического исключения
     *
     * @access public
     * @param string $name
     * @param array $args
     * @return void
     */
    public function __call($name, $args)
    {
        if (preg_match('/^exception/', $name))
            return call_user_func_array(array(Core, $name), $args);
    }

    /**
     * Генерация вызванного статического исключения
     *
     * @access public
     * @param string $name
     * @param array $args
     * @return void
     */
    public static function __callStatic($name, $args)
    {
        if (preg_match('/^exception[A-Z]/', $name))
            return call_user_func_array(array(\Core, $name), $args);
    }
}
