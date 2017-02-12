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
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        if (!$this->cursor) {
            $this->cursor = $this->factory($this->_cursorFactory);
        }
        return $this->cursor->runQuery('SHOW TABLES');
    }

    /**
     * Сброс результатов выполнения последнего запроса
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reset()
    {
        if ($this->_cursor) {
            $this->_cursor->free();
            unset($this->_cursor);
        }
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
