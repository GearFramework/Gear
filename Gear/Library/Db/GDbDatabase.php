<?php

namespace Gear\Library\Db;

use Gear\Library\GModel;
use Gear\Library\GEvent;
use Gear\Traits\TDelegateFactory;
use Gear\Traits\TFactory;

/**
 * Библиотека базы данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbDatabase extends GModel implements \IteratorAggregate
{
    /* Traits */
    use TFactory;
    use TDelegateFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_factoryProperties = [
        'class' => '\Gear\Library\Db\GDbCollection',
    ];
    protected $_cursorFactory = [
        'class' => '\Gear\Library\Db\GDbCursor',
    ];
    protected $_handler = null;
    protected $_items = [];
    protected $_current = null;
    /* Public */

    /**
     * Генерация события после выбоа базы данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterSelect()
    {
        return $this->trigger('onAfterConnect', new GEvent($this, ['target' => $this]));
    }

    /**
     * Генерация события до выбора базы данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeSelect()
    {
        return $this->trigger('onBeforeConnect', new GEvent($this, ['target' => $this]));
    }

    /**
     * Создание базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function create(): GDbDatabase;

    /**
     * Удаление базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function drop(): GDbDatabase;

    /**
     * Возвращает подключение к серверу базы данных
     *
     * @return GDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): GDbConnection
    {
        return $this->owner;
    }

    /**
     * Возвращает текущую выбранную коллекцию
     *
     * @return null|GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrent(): ?GDbCollection
    {
        return $this->_current;
    }

    /**
     * Возвращает ресурс подключения к базе данных
     *
     * @return null|resource
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHandler()
    {
        return $this->getConnection()->handler;
    }

    /**
     * Удаление базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove(): GDbDatabase
    {
        $this->drop();
    }

    /**
     * Выбор текущей базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function select(): GDbDatabase;

    /**
     * Возвращает коллекцию
     *
     * @param string $name
     * @param string $alias
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $name, string $alias = ''): GDbCollection
    {
        if (isset($this->_items[$name])) {
            if ($alias) {
                $this->_items[$name]->alias = $alias;
            }
            if (!$this->current || $this->current->name !== $name) {
                $this->current = $this->_items[$name];
            }
        } else {
            $properties = $alias ? ['name' => $name, 'alias' => $alias] : ['name' => $name];
            $this->current = $this->_items[$name] = $this->factory($properties, $this);
        }
        return $this->current;
    }

    /**
     * Установка текущей коллекции
     *
     * @param GDbCollection $current
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCurrent(GDbCollection $current)
    {
        $this->_current = $current;
    }
}
