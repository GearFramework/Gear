<?php

namespace Gear\Traits\Objects;

use ArrayAccess;

/**
 * Сеттер значения для неопределённого поля в классе
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait SetterTrait
{
    /**
     * Сеттер значения для неопределённого поля в классе
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set(string $name, mixed $value): void
    {
        $setterMethod = 'set' . ucfirst($name);
        if (method_exists($this, $setterMethod)) {
            $this->$setterMethod($value);
            return;
        }
        if ($this instanceof ArrayAccess) {
            $this[$name] = $value;
            return;
        }
    }
}
