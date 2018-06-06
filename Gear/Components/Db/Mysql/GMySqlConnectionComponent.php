<?php

namespace Gear\Components\Db\Mysql;

use Gear\Core;
use Gear\Library\Db\GDbConnection;
use Gear\Library\GEvent;

/**
 * Компонент подключения к MySql-серверу
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GMySqlConnectionComponent extends GDbConnection
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected static $_defaultProperties = [
        'host' => 'localhost',
        'user' => '',
        'password' => '',
        'database' => null,
        'port' => 3306,
        'socket' => null,
        'charset' => 'utf8',
        'collate' => 'utf8_general_ci',
        'autoConnect' => true,
    ];
    protected $_factory = [
        'class' => '\gear\components\db\mysql\GMySqlDatabase',
    ];
    protected $_cursorFactory = [
        'class' => '\gear\components\db\mysql\GMySqlCursor',
    ];
    /* Public */

    /**
     * Завершение соединения с сервером баз данных
     *
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    public function close(): GDbConnection
    {
        if ($this->isConnected()) {
            $this->handler->close();
        }
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
        $record = ['name' => $record['Database']];
        return $record ? array_replace_recursive($this->_factory, $record) : $this->_factory;
    }

    /**
     * Возвращает итератор со списком баз данных на сервере
     *
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator(): \Iterator
    {
        return $this->delegate($this->cursor->runQuery('SHOW DATABASES'));
    }

    /**
     * Обработчик события onAfterConnect, возникающего после подключения к серверу
     *
     * @param GEvent $event
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function onAfterConnect(GEvent $event): bool
    {
        if ($this->isConnected()) {
            $this->handler->set_charset($this->charset);
        }
        return true;
    }

    /**
     * Обработчик события onAfterConnect, возникающего после установки компонента
     *
     * @param GEvent $event
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function onAfterInstallService(GEvent $event): bool
    {
        if ($this->autoConnect) {
            $this->open();
        }
        return true;
    }

    /**
     * Подключение к серверу баз данных
     *
     * @return GDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function open(): GDbConnection
    {
        if (!$this->isConnected()) {
            $this->handler = new \mysqli($this->host, $this->user, $this->password, $this->database, $this->this, $this->socket);
            if ($this->handler->connect_error) {
                throw self::exceptionDatabaseConnection('Error connecting to database server <{user}@{host}>', ['user' => $this->user, 'host' => $this->host]);
            }
        }
        return $this;
    }
}
