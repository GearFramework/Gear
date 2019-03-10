<?php

namespace Gear\Components\Db\MySql;

use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Library\Db\GDbConnection;
use Gear\Library\GEvent;

/**
 * Компонент подключения к MySql-серверу
 *
 * @package Gear Framework
 *
 * @property bool autoConnect
 * @property string charset
 * @property string collate
 * @property DbCursorInterface cursor
 * @property array cursorFactory
 * @property array factoryProperties
 * @property string host
 * @property string password
 * @property int port
 * @property string socket
 * @property string user
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GMySqlConnectionComponent extends GDbConnection implements DbConnectionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
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
    protected $_cursorFactory = [
        'class' => '\Gear\Components\Db\MySql\GMySqlCursor',
    ];
    protected $_factoryProperties = [
        'class' => '\Gear\Components\Db\MySql\GMySqlDatabase',
    ];
    /* Public */

    /**
     * Завершение соединения с сервером баз данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function close(): DbConnectionInterface
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
        return $record ? array_replace_recursive($this->_factoryProperties, $record) : $this->_factory;
    }

    /**
     * Возвращает итератор со списком баз данных на сервере
     *
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator(): iterable
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
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function open(): DbConnectionInterface
    {
        if (!$this->isConnected()) {
            $this->handler = new \mysqli($this->host, $this->user, $this->password, $this->database, $this->this, $this->socket);
            if ($this->handler->connect_error) {
                throw self::DatabaseConnectionException('Error connecting to database server <{user}@{host}>', ['user' => $this->user, 'host' => $this->host]);
            }
        }
        return $this;
    }
}
