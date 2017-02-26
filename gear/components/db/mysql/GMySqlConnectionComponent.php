<?php

namespace gear\components\db\mysql;

use gear\Core;
use gear\library\db\GDbConnection;
use gear\library\GEvent;

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
        'database' => '',
        'port' => '',
        'socket' => '',
        'charser' => 'utf8',
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
    public function close()
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
        Core::syslog(Core::INFO, 'After connected handler', ['__func__' => __METHOD__, '__line__' => __LINE__]);
        if ($this->isConnected()) {
            $this->handler->set_charset($this->charser);
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
        Core::syslog(Core::INFO, 'Installed mysql db service', ['__func__' => __METHOD__, '__line__' => __LINE__]);
        if ($this->autoConnect) {
            Core::syslog(Core::INFO, 'Autoconnect active', ['__func__' => __METHOD__, '__line__' => __LINE__]);
            $this->connect();
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
            Core::syslog(Core::INFO, 'Connecting to MySql server <:host> by user <:user>', ['host' => $this->host, 'user' => $this->user, '__func__' => __METHOD__, '__line__' => __LINE__]);
            $this->handler = new \mysqli($this->host, $this->user, $this->password, $this->database, $this->this, $this->socket);
            if ($this->handler->connect_error) {
                throw self::exceptionDatabaseConnection('Error connecting to database server <{user}@{host}>', ['user' => $this->user, 'host' => $this->host]);
            }
        }
        return $this;
    }
}
