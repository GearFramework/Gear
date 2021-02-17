<?php

namespace Gear\Components\Db\MySql;

use Gear\Interfaces\DbDatabaseInterface;
use Gear\Library\Db\GDbDatabase;

/**
 * Библиотека базы данных
 *
 * @package Gear Framework
 *
 * @property GMySqlConnectionComponent connection
 * @property \mysqli handler
 * @property string name
 * @property GMySqlConnectionComponent owner
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GMySqlDatabase extends GDbDatabase implements DbDatabaseInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static array $_defaultProperties = [
        'name' => '',
    ];
    protected $_cursorFactory = [
        'class' => '\Gear\Components\Db\MySql\GMySqlCursor',
    ];
    protected $_factoryProperties = [
        'class' => '\Gear\Components\Db\MySql\GMySqlCollection',
    ];
    protected $_model = [
        'class' => '\Gear\Components\Db\MySql\GMySqlCollection',
    ];
    /* Public */

    /**
     * Создание базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function create(): DbDatabaseInterface
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
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function drop(): DbDatabaseInterface
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
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function select(): DbDatabaseInterface
    {
        $res = $this->handler->select_db($this->name);
        return $this;
    }
}
