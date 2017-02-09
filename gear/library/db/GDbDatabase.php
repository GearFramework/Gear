<?php

namespace gear\library\db;

use gear\library\GObject;

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
abstract class GDbDatabase extends GObject implements \Iterator
{
    /* Traits */
    use TFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected $_factory = [
        'class' => '\gear\library\db\GDbCollection',
    ];
    protected $_cursorFactory = [
        'class' => '\gear\library\db\GDbCursor',
    ];
    protected $_items = [];
    protected $_current = null;
    /* Public */

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
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function drop();

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
     * @return null|GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrent()
    {
        return $this->_current;
    }

    /**
     * Возвращает инстанс курсора для работы с запросами
     *
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): GDbCursor
    {
        return $this->factory($this->_cursorFactory);
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
        return $this->owner->handler;
    }

    /**
     * Удаление базы данных
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove()
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
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $name): GDbCollection
    {
        if (isset($this->_items[$name])) {
            if (!$this->current || $this->current->name !== $name) {
                $this->current = $this->_items[$name];
            }
        } else {
            $this->current = $this->_items[$name] = $this->factory(['name' => $name], $this);
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
