<?php

namespace Gear\Traits;

use Gear\Core;
use Gear\Interfaces\ComponentContainedInterface;
use Gear\Interfaces\ComponentInterface;
use Gear\Interfaces\DependentInterface;
use Gear\Interfaces\EventInterface;
use Gear\Interfaces\ObjectInterface;
use Gear\Interfaces\PluginContainedInterface;
use Gear\Interfaces\PluginInterface;
use Gear\Library\GEvent;

/**
 * Трэйт для добавления объектам базовых свойств и методов
 *
 * @package Gear Framework
 *
 * @property int access
 * @property array getters
 * @property ObjectInterface owner
 * @property array setters
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait ObjectTrait
{
    /**
     * Обработка вызовов несуществующих статических методов класса
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \ComponentNotFoundException
     * @throws \CoreException
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists(get_class($this), $name)) {
            $class = get_class($this);
            $class::$name(...$arguments);
        } elseif (preg_match('/Exception$/', $name)) {
            array_unshift($arguments, $name);
            return Core::e(...$arguments);
        } elseif (preg_match('/^on[A-Z]/', $name)) {
            if (isset($arguments[0]) && $arguments[0] instanceof EventInterface) {
                $event = $arguments[0];
                $event->target = $this;
            } else {
                $arguments = ['target' => $this];
                $event = new GEvent($this, $arguments);
            }
            return method_exists($this, 'trigger') ? $this->trigger($name, $event) : Core::trigger($name, $event);
        } elseif ($this instanceof ComponentContainedInterface && $this->isComponent($name)) {
            $component = $this->c($name);
            if (!is_callable($component)) {
                throw self::ObjectException('Component object <{componentName}> not callable and cannot be execute as function', ['componentName' => $name]);
            }
            return $component(...$arguments);
        } elseif ($this instanceof PluginContainedInterface && $this->isPlugin($name)) {
            $plugin = $this->p($name);
            if (!is_callable($plugin)) {
                throw self::ObjectException('Plugin object <{pluginName}> not callable and cannot be execute as function', ['pluginName' => $name]);
            }
            return $plugin(...$arguments);
        } else {
            if ($this instanceof ComponentContainedInterface) {
                foreach ($this->getComponents() as $componentName => $component) {
                    $component = $this->c($componentName);
                    if (method_exists($component, $name)) {
                        return $component->$name(...$arguments);
                    }
                }
            }
            if ($this instanceof PluginContainedInterface) {
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
                return $this instanceof DependentInterface ? $this->owner->$name($this, ...$arguments) : $this->owner->$name(...$arguments);
            }
        }
        throw self::ObjectException('Calling method <{methodName}> not exists in class <{class}>', ['methodName' => $name, 'class' => get_class($this)]);
    }

    /**
     * Обработка вызовов несуществующих статических методов класса
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (preg_match('/Exception$/', $name)) {
            array_unshift($arguments, $name);
            return Core::e(...$arguments);
        } elseif (preg_match('/^on[A-Z]/', $name)) {
            array_unshift($arguments, $name);
            return Core::trigger(...$arguments);
        }
        throw self::ObjectException('Static method <{methodName}> not exists', ['methodName' => $name]);
    }

    /**
     * GObject constructor.
     *
     * @param array|\Closure $properties
     * @param null|ObjectInterface $owner
     * @since 0.0.1
     * @version 0.0.2
     */
    protected function __construct($properties = [], ?ObjectInterface $owner = null)
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

    public function __get(string $name)
    {
        $value = null;
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            $value = $this->$getter();
        } elseif (preg_match('/^on[A-Z]/', $name)) {
            $value = method_exists($this, 'trigger') ?
                $this->trigger($name, new GEvent($this, ['target' => $this])) :
                Core::trigger($name, new GEvent($this, ['target' => $this]));
        } elseif ($this instanceof ComponentContainedInterface && $this->isComponent($name)) {
            $value = $this->c($name);
        } elseif ($this instanceof PluginContainedInterface  && $this->isPlugin($name)) {
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

    public function __set(string $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (preg_match('/^on[A-Z]/', $name) && method_exists($this, 'on')) {
            $this->on($name, $value);
        } else {
            if (method_exists($this, 'installComponent') && $value instanceof ComponentInterface) {
                $this->installComponent($value);
            } elseif (method_exists($this, 'installPlugin') && $value instanceof PluginInterface) {
                $this->installPlugin($name, $value);
            } else {
                $this->_properties[$name] = $value;
            }
        }
    }

    /**
     * Возвращает название класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __toString(): string
    {
        return static::class;
    }

    /**
     * Возвращает название класса из пространства имён.
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getClassName(): string
    {
        return Core::getClassName(static::class);
    }

    /**
     * Возвращает название пространства имён класса без названия самого класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function getNamespace(): string
    {
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
     * Возвращает режим доступа к объекту
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAccess(): int
    {
        return $this->_access;
    }

    /**
     * Возвращает набор геттеров
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getGetters(): array
    {
        return $this->_getters;
    }

    /**
     * Возвращает владельца объекта
     *
     * @return null|ObjectInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getOwner(): ?ObjectInterface
    {
        return $this->_owner;
    }

    /**
     * Возвращает набор сеттеров
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getSetters(): array
    {
        return $this->_setters;
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
     * Устанавливает режим доступа к объекту
     *
     * @param int $access
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAccess(int $access)
    {
        $this->_access = $access;
    }

    /**
     * Устанавливает набор геттеров
     *
     * @param array $getters
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setGetters(array $getters)
    {
        $this->_getters = $getters;
    }

    /**
     * Установка владельца объекта
     *
     * @param ObjectInterface $owner
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setOwner(ObjectInterface $owner)
    {
        $this->_owner = $owner;
    }

    /**
     * Устанавливает набор сеттеров
     *
     * @param array $setters
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setSetters(array $setters)
    {
        $this->_setters = $setters;
    }
}