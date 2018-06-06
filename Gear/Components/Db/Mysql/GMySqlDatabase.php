<?php

namespace Gear\Components\Db\Mysql;

use Gear\Library\Db\GDbDatabase;

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
class GMySqlDatabase extends GDbDatabase
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_defaultProperties = [
        'name' => '',
    ];
    protected $_factoryProperties = [
        'class' => '\Gear\Components\Db\Mysql\GMySqlCollection',
    ];
    protected $_cursorFactory = [
        'class' => '\Gear\Components\Db\Mysql\GMySqlCursor',
    ];
    /* Public */

    /**
     * Создание базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create(): GDbDatabase
    {
        $this->cursor->runQuery(
            'CREATE DATABASE `%s` CHARACTER SET %s COLLATE %s',
            $this->name,
            $this->connection->charset,
            $this->connection->collate
        );
        return $this->select();
    }

    /**
     * Удаление базы данных
     *
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    public function drop(): GDbDatabase
    {
        $this->cursor->runQuery('DROP DATABASE `%s`', $this->name);
        return $this;
    }

    /**
     * Возвращает данные создаваемого объекта
     *
     * @param array $record
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getFactory(array $record = []): array
    {
        $record = ['name' => reset($record)];
        return $record ? array_replace_recursive($this->_factory, $record) : $this->_factory;
    }

    /**
     * Возвращает итератор со списком таблиц в базе данных
     *
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator(): \Iterator
    {
        return $this->delegate($this->cursor->runQuery('SHOW TABLES'));
    }

    /**
     * Выбор текущей базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function select(): GDbDatabase
    {
        $this->handler->select_db($this->name);
        return $this;
    }
}
