<?php

namespace Gear\Traits\Objects;

use ArrayAccess;
use Gear\Interfaces\Services\ComponentContainedInterface;
use Gear\Interfaces\Services\ComponentInterface;
use Gear\Interfaces\Services\PluginContainedInterface;
use Gear\Interfaces\Services\PluginInterface;

/**
 * Геттер для неопределённого поля в классе
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait GetterTrait
{
    /**
     * Геттер для неопределённого поля в классе
     *
     * @param   string $name
     * @return  mixed
     */
    public function __get(string $name): mixed
    {
        $getterMethod = 'get' . ucfirst($name);
        if (method_exists($this, $getterMethod)) {
            return $this->$getterMethod();
        }
        if ($this instanceof ArrayAccess) {
            return $this[$name];
        }
        if ($this instanceof ComponentContainedInterface) {
            $component = $this->c($name);
            if ($component instanceof ComponentInterface) {
                return $component;
            }
        }
        if ($this instanceof PluginContainedInterface) {
            $plugin = $this->p($name);
            if ($plugin instanceof PluginInterface) {
                return $plugin;
            }
        }
        return null;
    }
}
