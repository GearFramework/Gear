<?php

namespace gear\traits;
use gear\interfaces\IComponent;
use gear\interfaces\IObject;

/**
 * Трэйт для объектов, которым необходимо поддерживать компоненты
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TComponentContained
{
    /**
     * @var array $_components массив установленных компонентов у объекта
     */
    protected $_components = [];

    /**
     * Автозагрузка необходимых компонентов во время создания объекта-контейнера
     *
     * @param array $components
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _bootstrapComponents(array $components)
    {
        foreach($components as $name => $component) {
            $this->installComponent($name, $component);
        }
    }

    /**
     * Возвращает установелнный компонент
     *
     * @param string $name
     * @param IObject|null $owner
     * @throws \ComponentNotFoundException
     * @return IComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public function c(string $name, IObject $owner = null): IComponent
    {
        if (!($component = $this->isComponentInstalled($name))) {
            if (!($component = $this->isComponentRegistered($name)))
                throw static::exceptionComponentNotFound(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
            $component = $this->installComponent($name, $component, $owner);
        }
        return $component;
    }

    /**
     * Проверка на наличие указанного компонента. Возвращает инстанс компонента или false, если такой не был найден
     *
     * @param string $name
     * @return bool|IComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isComponent(string $name)
    {
        if (!($component = isset($this->_components[$name]))) {
            $component = static::i('components');
            $component = isset($component[$name]) ? $this->installComponent($name, $component[$name]) : false;
        }
        return $component;
    }

    /**
     * Возвращает конфигурационную запись зарегистрированного компонента, иначе возвращается false
     *
     * @param string $name
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isComponentRegistered(string $name)
    {
        $component = false;
        if (isset(static::$_config['components'][$name]))
            $component = static::$_config['components'][$name];
        else if (isset(self::$_config['components'][$name]))
            $component = self::$_config['components'][$name];
        return $component;
    }

    /**
     * Регистрация компонента
     *
     * @param string $name
     * @param array|\Closure $component
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function registerComponent(string $name, $component)
    {
        if ($component instanceof \Closure)
            $component = $component($name);
        if (!is_array($component))
            throw static::exceptionComponentRegisteringIsInvalid(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        static::$_config['components'][$name] = $component;
    }

    /**
     * Возвращает компонент если он установлен, иначе возвращает false
     *
     * @param string $name
     * @return bool|IComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isComponentInstalled(string $name)
    {
        return isset($this->_components[$name]) ? $this->_components[$name] : false;
    }

    /**
     * Установка компонента
     *
     * @param string $name
     * @param array|IComponent|\Closure $component
     * @param IObject|null $owner
     * @return IComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public function installComponent(string $name, $component, $owner = null): IComponent
    {
        if ($component instanceof \Closure) {
            $component = $component($name, $owner);
        }
        if (is_array($component)) {
            list($class, $config, $properties) = \gear\Core::configure($component);
            $component = $class::install($config, $properties, $owner === null ? $this : $owner);
        }
        if (!($component instanceof \gear\interfaces\IComponent))
            throw static::exceptionComponentInstallationIsInvalid(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        $this->_components[$name] = $component;
        return $component;
    }

    /**
     * Деинсталляция компонента
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallComponent(string $name)
    {
        if (isset($this->_components[$name])) {
            $this->c($name)->uninstall();
            unset($this->_components[$name]);
        }
    }

    /**
     * Возвращает массив установленных компонентов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getComponents(): array
    {
        return $this->_components;
    }
}