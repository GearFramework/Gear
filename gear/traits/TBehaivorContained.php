<?php

namespace gear\traits;

use gear\interfaces\IBehavior;

/**
 * Трэйт для объектов, которым необходимо поддерживать поведения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TBehaviorContained
{
    /**
     * @var array $_behaviors массив установленных поведений у объекта
     */
    protected $_behaviors = [];

    /**
     * Вызов указнного поведения
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function b(string $name)
    {
        if (!($b = $this->isBehavior($name)))
            throw static::exceptionBehaviorNotAllowed(['name' => $name, 'file' => __FILE__, 'line' => __LINE__]);
        return $b();
    }

    /**
     * Возвращает объект поведение, если такой присутствует у класса объета, либо false
     *
     * @param string $name
     * @return bool|IBehavior|\Closure
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isBehavior(string $name)
    {
        if (!isset($this->_behaviors[$name])) {
            $b = static::i('behaviors');
            $b = isset($b[$name]) ? $this->attachBehavior($name, $b[$name]) : false;
        } else {
            $b = $this->_behaviors[$name];
        }
        return $b;
    }

    /**
     * Подключение к объекту набор поведений
     *
     * @param array $behaviors
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function attachBehaviors(array $behaviors)
    {
        foreach($behaviors as $name => $b) {
            $this->attachBehavior($name, $b);
        }
    }

    /**
     * Подключение к объекту поведения
     *
     * @param string $name
     * @param array|\Closure|IBehavior $behavior
     * @return object
     * @since 0.0.1
     * @version 0.0.1
     */
    public function attachBehavior(string $name, $behavior)
    {
        if ($behavior instanceof \Closure) {
            $behavior->bindTo($this);
            $this->_behaviors[$name] = $behavior;
        } else if (is_array($behavior)) {
            list($class, $config, $properties) = \gear\Core::configure($behavior);
            $this->_behaviors[$name] = $class::install($config, $properties, $this);
        } else if ($behavior instanceof IBehavior) {
            $this->_behaviors[$name] = $behavior;
        } else {
            throw static::exceptionBehaviorInvalid(['name' => $name, 'file' => __FILE__, 'line' => __LINE__]);
        }
        return $this->_behaviors[$name];
    }

    /**
     * Удаление из объекта набора поведений
     *
     * @param array $names
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function detachBehaviors(array $names)
    {
        foreach($names as $name) {
            $this->detachBehavior($name);
        }
    }

    /**
     * Удаление из объекта указаное поведение
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function detachBehavior(string $name)
    {
        if (isset($this->_behaviors[$name])) {
            if ($this->_behaviors[$name] instanceof IBehavior)
                $this->_behaviors[$name]->uninstall();
            unset($this->_behaviors[$name]);
        }
    }
}