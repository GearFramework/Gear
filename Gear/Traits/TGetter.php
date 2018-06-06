<?php

namespace Gear\Traits;

use Gear\Interfaces\IDependent;

/**
 * Трейт для реализации геттеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TGetter
{
    /**
     * Вызов геттера
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __get(string $name)
    {
        $result = null;
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            $result = $this->$getter();
        } elseif (method_exists($this, 'isProperty') && !$this->isProperty($name) && $this instanceof IDependent) {
            $result = $this->owner->$name;
        } elseif (method_exists($this, 'props')) {
            $result = $this->props($name);
        }
        return $result;
    }
}
