<?php

namespace gear\components\log;

use gear\Core;
use gear\interfaces\IPlugin;
use gear\library\GComponent;

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
    protected static $_config = [];
    protected static $_initialized = false;
    protected $_loggers = [];
    /* Public */

    public function log($level, $message, array $context = [])
    {
        foreach($context as $name => $value) {
            $message = str_replace('{' . $name . '}', $value, $message);
        }
        foreach($this->loggers as $logger) {
            $this->p($logger)->log($level, $message, $context);
        }
    }

    public function alert($message, array $context = [])
    {
        $this->log(Core::ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(Core::CRITICAL, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(Core::DEBUG, $message, $context);
    }

    public function emergency($message, array $context = [])
    {
        $this->log(Core::EMERGENCY, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(Core::ERROR, $message, $context);
    }

    public function exception($message, array $context = [])
    {
        $this->log(Core::EXCEPTION, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(Core::INFO, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(Core::NOTICE, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(Core::WARNING, $message, $context);
    }

    public function setLoggers(array $loggers)
    {
        $this->_loggers = $loggers;
    }

    public function getLoggers()
    {
        return $this->_loggers;
    }

    public function addLogger($name, $logger)
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

    public function removeLogger($name)
    {
        $key = array_search($name, $this->_loggers);
        if ($key !== false)
            unset($this->_loggers[$key]);
    }
}