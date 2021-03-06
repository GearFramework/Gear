<?php

namespace Gear\Traits\Factory;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;
use Gear\Library\GDelegateFactoriableIterator;

/**
 * Делегация фабрики
 *
 * @package Gear Framework
 *
 * @property array|GDelegateFactoriableIterator delegate
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait DelegateFactoryTrait
{
    /**
     * @var array $_delegate параметры объекта, которому делегируется создание объектов
     */
    protected $_delegate = [
        'class' => '\Gear\Library\GDelegateFactoriableIterator',
    ];

    /**
     * Метод делегаци создания объектов фабрики
     *
     * @param iterable $source
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function delegate(iterable $source)
    {
        if (is_array($this->delegate)) {
            list($class,, $properties) = Core::configure($this->delegate);
            $this->delegate = new $class(array_merge($properties, ['source' => $source]), $this);
        } else {
            $this->delegate->source = $source;
        }
        return $this->delegate;
    }

    /**
     * Получение параметров или объекта, которому делегируется создание объектов
     * фабрики

     * @return array|ObjectInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getDelegate()
    {
        return $this->_delegate;
    }

    /**
     * Устновка параметров или объекта, которому делегируется создание объектов
     * фабрики
     *
     * @param array|ObjectInterface $delegate
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDelegate($delegate)
    {
        $this->_delegate = $delegate;
    }
}
