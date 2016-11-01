<?php

namespace gear\library;

use gear\Core;
use gear\interfaces\IObject;
use gear\traits\TBehaviorContained;
use gear\traits\TObject;
use gear\traits\TView;

/**
 * Базовый класс объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @property IObject|null owner
 * @property array _events
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GObject implements IObject
{
    /* Traits */
    use TObject;
    use TView;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    /**
     * @var array $_config конфигурация класса
     */
    protected static $_config = [
        'plugins' => [
            'view' => ['class' => '\gear\plugins\templater\GView']
        ]
    ];
    /**
     * @var array $_defaultProperties значения по-умолчанию для объектов класса
     */
    protected static $_sleepProperties = ['access', 'owner'];
    /**
     * @var array $_events события класса и их обработчики
     */
    protected static $_events = [];
    /**
     * @var int $_access режим доступа к объекту
     */
    protected $_access = Core::ACCESS_PUBLIC;
    /**
     * @var null|string пространство имён класса объекта
     */
    protected $_namespace = null;
    /**
     * @var null|object владелец объекта
     */
    protected $_owner = null;
    /* Public */

    /**
     * GObject constructor.
     *
     * @param array|\Closure $properties
     * @param \gear\interfaces\IObject|null $owner
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
        if ($owner)
            $this->owner = $owner;
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
        $this->afterConstruct();
    }

    /**
     * Клонирование объекта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __clone() {}

    /**
     * Возвращает название класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __toString()
    {
        return static::class;
    }

    /**
     * Обработка вызовов несуществующих статических методов класса
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \ObjectException
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (strpos($name, 'exception') === 0) {
            array_unshift($arguments, $name);
            return Core::e(...$arguments);
        } else if (preg_match('/^on[A-Z]/', $name)) {
            array_unshift($arguments, $name);
            return Core::trigger(...$arguments);
        }
        throw self::exceptionObject('Static method <{methodName}> not exists', ['methodName' => $name]);
    }

    /**
     * Обработка вызовов несуществующих статических методов класса
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \ObjectException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __call(string $name, array $arguments)
    {
        if (strpos($name, 'exception') === 0) {
            array_unshift($arguments, $name);
            return Core::e(...$arguments);
        } else if (preg_match('/^on[A-Z]/', $name)) {
            if (isset($arguments[0]) && $arguments[0] instanceof GEvent) {
                $event = $arguments[0];
                $event->target = $this;
            } else {
                if (isset($arguments[1]) && is_array($arguments[1]))
                    $arguments[1]['target'] = $this;
                else
                    $arguments = ['target' => $this];
                $event = new GEvent($this, $arguments);
            }
            $this->trigger($name, $event);
        } else if (method_exists($this, 'isBehavior') && ($b = $this->isBehavior($name))) {
            return $b(...$arguments);
        } else if (method_exists($this, 'isComponent') && $this->isComponent($name)) {
            $c = $this->c($name);
            if (!is_callable($c))
                throw self::exceptionObject('Component object <{componentName}> not callable and cannot be execute as function', ['componentName' => $name]);
            return $c(...$arguments);
        } else if (method_exists($this, 'isPlugin') && $this->isPlugin($name)) {
            $p = $this->p($name);
            if (!is_callable($p))
                throw self::exceptionObject('Plugin object <{pluginName}> not callable and cannot be execute as function', ['pluginName' => $name]);
            return $p(...$arguments);
        } else {
            if (method_exists($this, 'getComponents')) {
                foreach ($this->getComponents() as $componentName => $component) {
                    $component = $this->c($componentName);
                    if (method_exists($component, $name)) {
                        return $component->$name(...$arguments);
                    }
                }
            }
            if (method_exists($this, 'getPlugins')) {
                foreach ($this->getPlugins() as $pluginName => $plugin) {
                    $plugin = $this->p($pluginName);
                    if (method_exists($plugin, $name)) {
                        return $plugin->$name(...$arguments);
                    }
                }
            }
            if (isset($this->{_properties[$name]}) && $this->{_properties[$name]} instanceof \Closure) {
                return $this->{_properties[$name]}(...$arguments);
            } else if (is_object($this->owner)) {
                return $this->owner->$name(...$arguments);
            }
        }
        throw self::exceptionObject('Calling method <{methodName}> not exists in class <{class}>', ['methodName' => $name, 'class' => get_class($this)]);
    }

    public function __set(string $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else if (preg_match('/^on[A-Z]/', $name)) {
            $this->on($name, $value);
        } else {
            if (method_exists($this, 'installComponent') && $value instanceof IComponent) {
                $this->installComponent($value);
            } else if (method_exists($this, 'installBehavior') && $value instanceof IBehavior) {
                $this->installBehavior($name, $value);
            } else if (method_exists($this, 'installPlugin') && $value instanceof IPlugin) {
                $this->installPlugin($name, $value);
            } else {
                if (isset($this->_properties[$name]))
                    $this->_properties[$name] = $value;
                else if (is_object($this->owner))
                    $this->owner->$name = $value;
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
        else if (preg_match('/^on[A-Z]/', $name)) {
            $value = $this->trigger($name, new GEvent($this, ['target' => $this]));
        } else if (method_exists($this, 'isBehavior') && ($b = $this->isBehavior($name))) {
            $value = $b;
        } else if (method_exists($this, 'isComponent') && $this->isComponent($name)) {
            $value = $this->c($name);
        } else if (method_exists($this, 'isPlugin') && $this->isPlugin($name)) {
            $value = $this->p($name);
        } else {
            if (isset($this->_properties[$name])) {
                $value = $this->_properties[$name];
            } else if (is_object($this->owner)) {
                $value = $this->owner->$name;
            }
        }
        return $value;
    }

    public function __isset(string $name)
    {
        if (preg_match('/^on[A-Z]/', $name)) {
            return method_exists($this, $name) || isset($this->_events[$name]);
        } else if (method_exists($this, 'isBehavior') && $this->isBehavior($name)) {
            return true;
        } else if (method_exists($this, 'isComponent') && $this->isComponent($name)) {
            return true;
        } else if (method_exists($this, 'isPlugin') && $this->isPlugin($name)) {
            return true;
        } else if ($this instanceof ISlave) {
            return isset($this->owner->$name);
        } else
            return array_key_exists($name, $this->_properties);
    }

    public function __unset(string $name)
    {
        if (preg_match('/^on[A-Z]/', $name)) {
            $this->off($name);
        } else if (method_exists($this, 'isBehavior') && $this->isBehavior($name)) {
            $this->detachBehavior($name);
        } else if (method_exists($this, 'isComponent') && $this->isComponent($name)) {
            $this->uninstallComponent($name);
        } else if (method_exists($this, 'isPlugin') && $this->isPlugin($name)) {
            $this->uninstallPlugin($name);
        } else if (array_key_exists($name, $this->_properties)) {
            unset($this->_properties[$name]);
        } else if (is_object($this->owner)) {
            unset($this->owner->name);
        }
    }

    /**
     * Возвращает спискок полей объекта для сериализации
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __sleep(): array
    {
        return array_merge(array_keys($this->props()), self::$_sleepProperties);
    }

    /**
     * Десериализация объекта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __wakeup() {}

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
        if ($name === null && $value === null)
            return static::$_config;
        else if ($name && $value === null) {
            $value = null;
            if (isset(static::$_config[$name]))
                $value = static::$_config[$name];
            else if (isset(self::$_config[$name]))
                $value = self::$_config[$name];
            return $value;
        } else if ($name && $value) {
            static::$_config[$name] = $value;
        }
        return null;
    }

    /**
     * Установка владельца объекта
     *
     * @param \gear\interfaces\IObject $owner
     * @throws \ObjectException
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setOwner(IObject $owner)
    {
        if (!is_object($owner))
            throw $this->exceptionObject('Owner must be a object');
        $this->_owner = $owner;
    }

    /**
     * Возвращает владельца объекта
     *
     * @return \gear\interfaces\IObject
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOwner(): IObject
    {
        return $this->_owner;
    }

    /**
     * Установка уровня доступа к объекту
     *
     * @param int $value одно из значений Core::ACCESS_PRIVATE|Core::ACCESS_PROTECTED|Core::ACCESS_PUBLIC
     * @return void
     * @see \gear\Core::ACCESS_PRIVATE
     * @see \gear\Core::ACCESS_PROTECTED
     * @see \gear\Core::ACCESS_PUBLIC
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAccess($value)
    {
        $this->_access = $value;
    }

    /**
     * Получение уровня доступа к объекту
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
     * Возвращает название пространства имён класса.
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getNamespace(): string
    {
        if (!$this->_namespace) {
            $class = get_class($this);
            $this->_namespace = substr($class, 0, strrpos($class, '\\'));
        }
        return $this->_namespace;
    }

    /**
     * Удаление обработчика/ов указанного события
     *
     * @param string|array $eventName
     * @param null|array|callable|\Closure $handler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function off(string $eventName, $handler = null)
    {
        if ($handler === null && isset($this->_events[$eventName]))
            unset($this->_events[$eventName]);
        else if (isset($this->_events[$eventName])) {
            foreach ($this->_events[$eventName] as $h) {
                if ($h === $handler)
                    unset($this->_events[$eventName]);
            }
        }
    }

    /**
     * Установка обработчика/ов на событие
     *
     * @param string|array $eventName
     * @param null|array|callable|\Closure $handler
     * @throws \ObjectException
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function on(string $eventName, $handler)
    {
        if (is_array($eventName)) {
            foreach ($eventName as $event => $handler) {
                $this->on($event, $handler);
            }
        } else if (is_array($handler)) {
            foreach ($handler as $callable) {
                $this->on($eventName, $callable);
            }
        } else {
            if (!is_callable($handler))
                throw self::exceptionObject('Event handler must be is callable');
            !isset($this->_events[$eventName]) ? $this->_events[$eventName] = [$handler] : $this->_events[$eventName][] = $handler;
        }
    }

    /**
     * Генерация события
     *
     * @param string $eventName
     * @param \gear\library\GEvent $event
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function trigger(string $eventName, GEvent $event)
    {
        $result = true;
        if (method_exists($this, $eventName)) {
            $result = $this->$eventName($event);
            if (!$event->bubble)
                return $result;
        }
        if (isset(self::$_events[$eventName])) {
            foreach(self::$_events[$eventName] as $handler) {
                if (call_user_func($handler, $event) === false)
                    $result = false;
                if (!$event->bubble)
                    break;
            }
        }
        return $result;
    }

    /**
     * Cобытие, выполняющееся до заполнения объекта свойствами
     *
     * @param array|\Closure $properties
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeConstruct($properties)
    {
        if (method_exists($this, 'restoreDefaultProperties'))
            $this->restoreDefaultProperties();
        return $this->trigger('onBeforeConstruct', new GEvent($this, ['target' => $this, 'properties' => $properties]));
    }

    /**
     * Событие, выполняющееся после заполнения объекта свойствами
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterConstruct()
    {
        if ($preloads = static::i('preloads')) {
            foreach($preloads as $sectionName => $section) {
                $preloadMethod = '_preload' . ucfirst($sectionName);
                if (method_exists($this, $preloadMethod)) {
                    $this->$preloadMethod($section);
                }
            }
        }
        return $this->trigger('onAfterConstruct', new GEvent($this, ['target' => $this]));
    }
}
