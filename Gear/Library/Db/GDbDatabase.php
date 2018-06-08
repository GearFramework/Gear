<?php

namespace Gear\Library\Db;

use Gear\Interfaces\IDbCollection;
use Gear\Interfaces\IDbConnection;
use Gear\Interfaces\IDbDatabase;
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
abstract class GDbDatabase extends GModel implements \IteratorAggregate, IDbDatabase
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
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function create(): IDbDatabase;

    /**
     * Удаление базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function drop(): IDbDatabase;

    /**
     * Возвращает подключение к серверу базы данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection
    {
        return $this->owner;
    }

    /**
     * Возвращает текущую выбранную коллекцию
     *
     * @return null|IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrent(): ?IDbCollection
    {
        return $this->_current;
    }

    /**
     * Возвращает курсор
     *
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): IDbCursor
    {
        return $this->factory($this->_cursorFactory, $this);
    }

    /**
     * Возвращает базу данных, т.е. саму себя
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDatabase(): IDbDatabase
    {
        return $this;
    }

    /**
     * Возвращает ресурс подключения к базе данных
     *
     * @return mixed
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
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove(): IDbDatabase
    {
        $this->drop();
    }

    /**
     * Выбор текущей базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function select(): IDbDatabase;

    /**
     * Возвращает коллекцию
     *
     * @param string $name
     * @param string $alias
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $name, string $alias = ''): IDbCollection
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
     * @param IDbCollection $current
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCurrent(IDbCollection $current)
    {
        $this->_current = $current;
    }
}
