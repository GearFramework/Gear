<?php

namespace Gear\Library\Db;

use Gear\Interfaces\DbCollectionInterface;
use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Interfaces\DbDatabaseInterface;
use Gear\Library\GComponent;
use Gear\Library\GEvent;
use Gear\Traits\DelegateFactoryTrait;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Компонент подключения к базе данных
 *
 * @package Gear Framework
 *
 * @property DbConnectionInterface connection
 * @property DbDatabaseInterface current
 * @property DbCursorInterface cursor
 * @property mixed handler
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GDbConnection extends GComponent implements \IteratorAggregate, DbConnectionInterface
{
    /* Traits */
    use FactoryTrait;
    use DelegateFactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_defaultProperties = [
        'host' => 'localhost',
        'user' => '',
        'password' => '',
        'port' => '',
    ];
    protected $_cursorFactory = [
        'class' => '\Gear\Library\Db\GDbCursor',
    ];
    protected $_handler = null;
    protected $_items = [];
    protected $_current = null;
    /* Public */

    /**
     * Генерация события onAfterConnect после подключения к серверу
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterConnect()
    {
        return $this->trigger('onAfterConnect', new GEvent($this, ['target' => $this]));
    }

    /**
     * Генерация события onBeforeConnect до подключения к серверу
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeConnect()
    {
        return $this->trigger('onBeforeConnect', new GEvent($this, ['target' => $this]));
    }

    /**
     * Завершение соединения с сервером баз данных
     *
     * @abstract
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function close(): DbConnectionInterface;

    /**
     * Подготовка и вызов метода непосредственного подключения к серверу баз данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function connect(): DbConnectionInterface
    {
        if ($this->beforeConnect()) {
            $this->open();
            $this->afterConnect();
        }
        return $this;
    }

    /**
     * Возвращает ссылку на компонент подключения базы данных, т.е. на самого себя
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface
    {
        return $this;
    }

    /**
     * Возвращает текущую выбранную базу данных
     *
     * @return null|DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCurrent(): DbDatabaseInterface
    {
        return $this->_current;
    }

    /**
     * Возвращает курсор
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCursor(): DbCursorInterface
    {
        return $this->factory($this->_cursorFactory, $this);
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
        return $this->_handler;
    }

    /**
     * Возвращает true если соединение уже установлено, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isConnected(): bool
    {
        return $this->_handler ? true : false;
    }

    /**
     * Подключение к серверу базы данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function open(): DbConnectionInterface;

    /**
     * Выполняет подключение к серверу, если соединение ещё не было
     * установлено
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function reconnect(): DbConnectionInterface
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        return $this;
    }

    /**
     * Выбор указанной базы данных и таблицы
     *
     * @param string $dbName
     * @param string $collectionName
     * @param string $alias
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectCollection(string $dbName, string $collectionName, string $alias = ''): DbCollectionInterface
    {
        return $this->selectDB($dbName)->selectCollection($collectionName, $alias);
    }

    /**
     * Выбор указанной базы данных
     *
     * @param string $name
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectDB(string $name): DbDatabaseInterface
    {
        if (isset($this->_items[$name])) {
            if (!$this->current || $this->current->name !== $name) {
                $this->current = $this->_items[$name];
                $this->current->select();
            }
        } else {
            $db = $this->factory(['name' => $name], $this);
            $this->current = $this->_items[$name] = $db;
            $this->current->select();
        }
        return $this->_current;
    }

    /**
     * Установка текущей базы данных
     *
     * @param DbDatabaseInterface $current
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setCurrent(DbDatabaseInterface $current)
    {
        $this->_current = $current;
    }

    /**
     * Устанавливает ресурс подключения к базе данных
     *
     * @param mixed $handler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
    }
}
