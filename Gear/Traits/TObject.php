<?php

namespace Gear\Traits;

use Gear\Core;
use Gear\Interfaces\IComponentContained;
use Gear\Interfaces\IDependent;
use Gear\Interfaces\IObject;
use Gear\Interfaces\IPluginContained;
use gear\library\GEvent;

/**
 * Трэйт для добавления объектам базовых свойств и методов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TObject
{
    /**
     * @var null|object владелец объекта
     */
    protected $_owner = null;

    /**
     * Обработка вызовов несуществующих статических методов класса
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \ComponentNotFoundException
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __call(string $name, array $arguments)
    {
        if (preg_match('/Exception$/', $name)) {
            array_unshift($arguments, $name);
            return Core::e(...$arguments);
        } elseif (preg_match('/^on[A-Z]/', $name)) {
            if (isset($arguments[0]) && $arguments[0] instanceof GEvent) {
                $event = $arguments[0];
                $event->target = $this;
            } else {
                $arguments = ['target' => $this];
                $event = new GEvent($this, $arguments);
            }
            return method_exists($this, 'trigger') ? $this->trigger($name, $event) : Core::trigger($name, $event);
        } elseif ($this instanceof IComponentContained && $this->isComponent($name)) {
            $c = $this->c($name);
            if (!is_callable($c)) {
                throw self::ObjectException('Component object <{componentName}> not callable and cannot be execute as function', ['componentName' => $name]);
            }
            return $c(...$arguments);
        } elseif ($this instanceof IPluginContained && $this->isPlugin($name)) {
            $p = $this->p($name);
            if (!is_callable($p)) {
                throw self::ObjectException('Plugin object <{pluginName}> not callable and cannot be execute as function', ['pluginName' => $name]);
            }
            return $p(...$arguments);
        } else {
            if ($this instanceof IComponentContained) {
                foreach ($this->getComponents() as $componentName => $component) {
                    $component = $this->c($componentName);
                    if (method_exists($component, $name)) {
                        return $component->$name(...$arguments);
                    }
                }
            }
            if ($this instanceof IPluginContained) {
                foreach ($this->getPlugins() as $pluginName => $plugin) {
                    $plugin = $this->p($pluginName);
                    if (method_exists($plugin, $name)) {
                        return $plugin->$name(...$arguments);
                    }
                }
            }
            if (method_exists($this, 'props') &&
                isset($this->_properties[$name]) &&
                $this->_properties[$name] instanceof \Closure) {
                return $this->_properties[$name](...$arguments);
            } elseif (is_object($this->owner)) {
                return $this instanceof IDependent ? $this->owner->$name($this, ...$arguments) : $this->owner->$name(...$arguments);
            }
        }
        throw self::ObjectException('Calling method <{methodName}> not exists in class <{class}>', ['methodName' => $name, 'class' => get_class($this)]);
    }

    public function __set(string $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (preg_match('/^on[A-Z]/', $name)) {
            $this->on($name, $value);
        } else {
            if (method_exists($this, 'installComponent') && $value instanceof IComponent) {
                $this->installComponent($value);
            } elseif (method_exists($this, 'installPlugin') && $value instanceof IPlugin) {
                $this->installPlugin($name, $value);
            } else {
                $this->_properties[$name] = $value;
            }
        }
    }

    public function __get(string $name)
    {
        $value = null;
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            $value = $this->$getter();
        }
        elseif (preg_match('/^on[A-Z]/', $name)) {
            $value = $this->trigger($name, new GEvent($this, ['target' => $this]));
        } elseif (method_exists($this, 'isComponent') && $this->isComponent($name)) {
            $value = $this->c($name);
        } elseif (method_exists($this, 'isPlugin') && $this->isPlugin($name)) {
            $value = $this->p($name);
        } else {
            if (isset($this->_properties[$name])) {
                $value = $this->_properties[$name];
            } elseif (is_object($this->owner)) {
                $value = $this->owner->$name;
            }
        }
        return $value;
    }

    /**
     * GObject constructor.
     *
     * @param array|\Closure $properties
     * @param null|IObject $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __construct($properties = [], IObject $owner = null)
    {
        $this->beforeConstruct($properties);
        if ($properties instanceof \Closure)
            $properties = $properties($this);
        if (!is_array($properties))
            $properties = [];
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
        if ($owner)
            $this->owner = $owner;
        $this->afterConstruct();
    }

    /**
     * Возвращает название класса из пространства имён.
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getClassName(): string {
        Core::getClassName(static::class);
    }

    /**
     * Возвращает название пространства имён класса без названия самого класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getNamespace(): string {
        return Core::getNamespace(static::class);
    }

    /**
     * Вызывается после создания объекта
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterConstruct()
    {
        return $this->onAfterConstruct(new GEvent($this));
    }

    /**
     * Вызывается перед созданием объекта
     *
     * @param array $properties
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeConstruct($properties)
    {
        if (method_exists($this, 'restoreDefaultProperties')) {
            $this->restoreDefaultProperties();
        }
        return $this->onBeforeConstruct(new GEvent($this, ['properties' => $properties]));
    }

    /**
     * Возвращает владельца объекта
     *
     * @return null|IObject
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOwner(): ?IObject
    {
        return $this->_owner;
    }

    /**
     * Получение/установка конфигурационных параметров класса
     *
     * @param null|string $name
     * @param mixed $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function i($name = null, $value = null)
    {
        if ($name === null && $value === null) {
            return static::$_config;
        } elseif ($name && $value === null) {
            $value = null;
            if (is_array($name)) {
                static::$_config = array_replace_recursive(static::$_config, $name);
            } elseif (isset(static::$_config[$name])) {
                $value = static::$_config[$name];
            } elseif (isset(self::$_config[$name])) {
                $value = self::$_config[$name];
            }
            return $value;
        } elseif ($name && $value) {
            static::$_config[$name] = $value;
        }
        return null;
    }

    /**
     * Установка владельца объекта
     *
     * @param IObject $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setOwner(IObject $owner)
    {
        $this->_owner = $owner;
    }
}