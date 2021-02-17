<?php

namespace Gear\Components\Db\MongoDb;

use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Library\Db\GDbConnection;
use Gear\Library\GEvent;
use Traversable;

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
 * @property \MongoDB handler
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
class GMongoDbConnectionComponent extends GDbConnection implements DbConnectionInterface
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
        'port' => 27017,
        'autoConnect' => true,
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
     * Обработчик события onAfterConnect, возникающего после установки компонента
     *
     * @param GEvent $event
     * @return bool
     * @throws \Exception
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
     * @throws \Exception
     * @since 0.0.1
     * @version 0.0.2
     */
    public function open(): DbConnectionInterface
    {
        $this->handler = new \MongoDB\Driver\Manager('mongodb://programmer:vohm7Eda@localhost:27017');
        if (!$this->isConnected()) {
            $this->handler->connect();
        }
        return $this;
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return $this->handler;
    }
}
