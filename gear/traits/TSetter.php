<?php

namespace gear\traits;
use gear\interfaces\IDependent;

/**
 * Трейт для реализации сеттеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TSetter
{
    /**
     * Вызов сеттера
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __set(string $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else if (method_exists($this, 'isProperty') && !$this->isProperty($name) && $this instanceof IDependent) {
            $this->owner->$name = $value;
        } else if (method_exists($this, 'props')) {
                $this->props($name, $value);
        }
    }
}
