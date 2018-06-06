<?php

namespace Gear\Components\Log;

use Gear\Core;
use Gear\Interfaces\IPlugin;
use Gear\Library\GComponent;

/**
 * Компонент логгирования операций
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GLogComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'plugins' => [
            'file' => [
                'class' => '\Gear\Plugins\Log\GFileLogger',
            ],
        ],
    ];
    protected static $_initialized = false;
    /**
     * @var array $_loggers список логгеров (названия плагинов, производящих логгирование)
     */
    protected $_loggers = ['file'];
    /* Public */

    /**
     * @param string $name
     * @param IPlugin|array $logger
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function addLogger(string $name, $logger)
    {
        $this->_loggers[] = $name;
        if ($logger instanceof IPlugin) {
            $this->installPlugin($name, $logger, $this);
        } else if (is_array($logger)) {
            $this->registerPlugin($name, $logger);
        } else {
            throw $this->exceptionInvalidLogWriter(['logger' => $name]);
        }
    }

    /**
     * Запись в лог сообщения типа ALERT
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function alert($message, array $context = [])
    {
        $this->log(Core::ALERT, $message, $context);
    }

    /**
     * Запись в лог сообщения типа CRITICAL
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function critical($message, array $context = [])
    {
        $this->log(Core::CRITICAL, $message, $context);
    }

    /**
     * Запись в лог сообщения типа DEBUG
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function debug($message, array $context = [])
    {
        $this->log(Core::DEBUG, $message, $context);
    }

    /**
     * Запись в лог сообщения типа EMERGENCY
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function emergency($message, array $context = [])
    {
        $this->log(Core::EMERGENCY, $message, $context);
    }

    /**
     * Запись в лог сообщения типа ERROR
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function error($message, array $context = [])
    {
        $this->log(Core::ERROR, $message, $context);
    }

    /**
     * Запись в лог сообщения типа EXCEPTION
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exception($message, array $context = [])
    {
        $this->log(Core::EXCEPTION, $message, $context);
    }

    /**
     * Возвращает массив именн используемых логгеров
     * Имя логгера - это название плагина данного компонента
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getLoggers(): array
    {
        return $this->_loggers;
    }

    /**
     * Запись в лог сообщения типа INFO
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function info($message, array $context = [])
    {
        $this->log(Core::INFO, $message, $context);
    }

    /**
     * Запись в лог
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function log(string $level, string $message, array $context = [])
    {
        foreach($context as $name => $value) {
            $message = str_replace('{' . $name . '}', $value, $message);
        }
        foreach($this->loggers as $logger) {
            $this->p($logger)->log($level, $message, $context);
        }
    }

    /**
     * Запись в лог сообщения типа NOTICE
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function notice($message, array $context = [])
    {
        $this->log(Core::NOTICE, $message, $context);
    }

    /**
     * Удаление логгера
     *
     * @param $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public function removeLogger($name)
    {
        if (($key = array_search($name, $this->_loggers, true)) !== false) {
            unset($this->_loggers[$key]);
        }
    }

    /**
     * Установка массива имен используемых логгеров
     * Имя логгера - это название плагина данного компонента
     *
     * @param array $loggers
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setLoggers(array $loggers)
    {
        $this->_loggers = $loggers;
    }

    /**
     * Запись в лог сообщения типа WARNING
     *
     * @param $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function warning($message, array $context = [])
    {
        $this->log(Core::WARNING, $message, $context);
    }
}
