<?php

namespace Gear\Traits;

use Gear\Core;

/**
 * Трейт хелперов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait THelper
{
    /**
     * Обработка и выполнение вызываемого метода
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $helper = 'help' . ucfirst($name);
        if (!method_exists(static::class, $helper)) {
            throw Core::ObjectException('Invalid helper method <{methodName}>', ['methodName' => $name]);
        }
        return static::$helper(...$arguments);
    }

    /**
     * Вызов статического метода хелпера
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __call(string $name, array $arguments)
    {
        return self::__callStatic($name, $arguments);
    }
}