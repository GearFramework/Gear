<?php

namespace Gear\Components\Nats;

use Gear\Core;
use Gear\Library\GComponent;
use Nats\Library\NatsConnection;
use Nats\Models\NatsConnectionOptions;

/**
 * Компонент для работы с NATS
 *
 * @package Gear Framework
 *
 * @property bool autoConnect
 * @property NatsConnection connection
 * @property string host
 * @property int port
 *
 * @author Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GNatsComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    private $_connection = null;
    /* Protected */
    protected static $_defaultProperties = [
        'autoConnect' => true,
        'context' => null,
        'host' => 'localhost',
        'port' => 4222,
    ];
    /* Public */

    public function connect(): bool
    {
        $options = new NatsConnectionOptions([
            'host' => $this->host,
            'port' => $this->port,
            'streamContext' => stream_context_get_default(),
        ]);
        $this->connection = new NatsConnection([
            'options' => $options,
        ]);
        try {
            $this->connection->connect();
            return true;
        } catch (\Exception $e) {
            Core::c('logger')->exception($e->getMessage(), ['e' => $e]);
            return false;
        }
    }

    public function getConnection(): ?NatsConnection
    {
        return $this->_connection;
    }

    public function onAfterInstallService()
    {
        if ($this->autoConnect) {
            $this->connect();
        }
        return true;
    }

    public function setConnection(NatsConnection $connection)
    {
        $this->_connection = $connection;
    }
}
