<?php

namespace gear\library\db;

use gear\library\GComponent;
use gear\traits\TFactory;

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
abstract class GDbConnection extends GComponent implements \Iterator
{
    /* Traits */
    use TFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected static $_defaultProperties = [
        'host' => 'localhost',
        'user' => '',
        'password' => '',
        'port' => '',
        'charser' => 'utf8',
        'collate' => 'utf8_general_ci',
    ];
    protected $_factory = [
        'class' => '\gear\library\db\GDbDatabase',
    ];
    protected $_handler = null;
    protected $_items = [];
    protected $_current = null;
    /* Public */

    /**
     * Завершение соединения с сервером баз данных
     *
     * @abstract
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function close();

    /**
     * Подключение к серверу баз данных
     *
     * @abstract
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function connect();

    /**
     * Возвращает текущую выбранную базу данных
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
     * Возвращает ресурс подключения к базе данных
     *
     * @return null|resource
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
     * Выполняет подключение к серверу, если соединение ещё не было
     * установлено
     *
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reconnect()
    {
        if (!$this->isConnected())
            $this->connect();
        return $this;
    }

    /**
     * Выбор указанной базы данных и таблицы
     *
     * @param string $dbName
     * @param string $collectionName
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $dbName, string $collectionName): GDbCollection
    {
        return $this->selectDB($dbName)->selectCollection($collectionName);
    }

    /**
     * Выбор указанной базы данных
     *
     * @param string $name
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(string $name): GDbDatabase
    {
        if (isset($this->_items[$name])) {
            if (!$this->_current || $this->_current->name !== $name) {
                $this->_current = $this->_items[$name]->select();
            }
        } else {
            $this->_current = $this->_items[$name] = $this->factory(['name' => $name], $this)->select();
        }
        return $this->_current;
    }

    /**
     * Установка текущей базы данных
     *
     * @param GDbDatabase $current
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCurrent(GDbDatabase $current)
    {
        $this->_current = $current;
    }
}
