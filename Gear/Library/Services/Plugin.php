<?php

namespace Gear\Library\Services;

use Gear\Interfaces\Services\PluginInterface;

/**
 * Класс плагинов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class Plugin extends Service implements PluginInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Геттер для неопределённого поля в классе
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        $getterMethod = 'get' . ucfirst($name);
        if (method_exists($this, $getterMethod)) {
            return $this->$getterMethod();
        }
        if ($this->isProperty($name)) {
            return $this->props($name);
        }
        return $this->owner->$name;
    }

    /**
     * Вызов метода объекта, из которого был вызван плагин
     *
     * @param   string $methodName
     * @param   array $args
     * @return  mixed
     */
    public function __call(string $methodName, array $args): mixed
    {
        return $this->owner->$methodName(...$args);
    }
}
