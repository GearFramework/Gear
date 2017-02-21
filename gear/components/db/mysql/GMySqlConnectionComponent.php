<?php

namespace gear\components\db\mysql;

use gear\library\db\GDbConnection;

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
    ];
    protected $_factory = [
        'class' => '\gear\components\db\mysql\GMysqlDatabase',
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
     * Подключение к серверу баз данных
     *
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    public function connect()
    {
        if (!$this->isConnected()) {
            $this->handler = new mysqli($this->host, $this->user, $this->password, $this->database, $this->this, $this->socket);
            if (!$this->handler->connect_error) {
                throw self::exceptionDatabaseConnection('Error connecting to database server <{user}@{host}>', ['user' => $this->user, 'host' => $this->host]);
            }
        }
        return $this;
    }

    public function getIterator()
    {
        $cursor = $this->cursor->runQuery('SHOW DATABASES');
        return new \gear\library\GDelegateFactoriableIterator(['source' => $cursor], $this);
    }
}
