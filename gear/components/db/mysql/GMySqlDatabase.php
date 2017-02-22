<?php

namespace gear\components\db\mysql;

use gear\library\db\GDbDatabase;
use Traversable;

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
    use TFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected $_factory = [
        'class' => '\gear\components\db\mysql\GMySqlCollection',
    ];
    protected $_cursorFactory = [
        'class' => '\gear\components\db\mysql\GMySqlCursor',
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
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function drop()
    {
        $this->cursor->runQuery('DROP DATABASE `%s`', $this->name);
        return $this;
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
