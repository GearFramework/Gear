<?php

namespace Gear\Library\Db;

use Gear\Interfaces\IDbCollection;
use Gear\Interfaces\IDbConnection;
use Gear\Interfaces\IDbCursor;
use Gear\Interfaces\IDbDatabase;
use Gear\Library\GComponent;
use Gear\Library\GEvent;
use Gear\Traits\TDelegateFactory;
use Gear\Traits\TFactory;

/**
 * Компонент подключения к базе данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbConnection extends GComponent implements \IteratorAggregate, IDbConnection
{
    /* Traits */
    use TFactory;
    use TDelegateFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
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
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function close(): IDbConnection;

    /**
     * Подготовка и вызов метода непосредственного подключения к серверу баз данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function connect(): IDbConnection
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
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection
    {
        return $this;
    }

    /**
     * Возвращает текущую выбранную базу данных
     *
     * @return null|IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrent(): ?IDbDatabase
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
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function open(): IDbConnection;

    /**
     * Выполняет подключение к серверу, если соединение ещё не было
     * установлено
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reconnect(): IDbConnection
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
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $dbName, string $collectionName, string $alias = ''): IDbCollection
    {
        return $this->selectDB($dbName)->selectCollection($collectionName, $alias);
    }

    /**
     * Выбор указанной базы данных
     *
     * @param string $name
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(string $name): IDbDatabase
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
     * @param IDbDatabase $current
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCurrent(IDbDatabase $current)
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
