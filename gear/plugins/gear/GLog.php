<?php

namespace gear\plugins\gear;
use gear\Core;
use gear\library\GPlugin;

/** 
 * Плагин ведения логов
 * 
 * @package Gear Framework
 * @plugin Log
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.3.x
 * @release 1.0.0
 */
class GLog extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_properties = array
    (
        'datetimeTemplate' => 'd/m/Y H:i:s',
    );
    /* Пути сохранения данных протоколирования */
    protected $_routes = array
    (
        'fileLog' => array
        (
            'class' => '\gear\plugins\gear\loggers\GFileLogger',
            'location' => 'logs',
            'templateFilename' => 'log-%Y-%m-%d.log',
            'levels' => array(Core::DEBUG, Core::CRITICAL, Core::WARNING, Core::ERROR),
            'maxLogFileSize' => '10MB',
        ),
    );
    /* Public */

    /**
     * Неявный вызов log()
     *
     * @access public
     * @return mixed
     */
    public function __invoke($level, $message, array $context = array())
    {
        return call_user_func(array($this, 'log'), $level, $message, $context);
    }

    /**
     * Уставнока путей сохранения данных протоколирования
     *
     * @access public
     * @param array $routes
     * @return $this
     */
    public function setRoutes(array $routes)
    {
        $this->_routes = $routes;
        return $this;
    }

    /**
     * Получение путей сохранения данных протоколирования
     *
     * @access public
     * @return string
     */
    public function getRoutes() { return $this->_routes; }

    /**
     * Запись сообщения
     *
     * @access public
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @throw SyslogException
     * @return $this
     */
    public function log($level, $message, array $context = array())
    {
        foreach($context as $param => $value)
            $message = str_replace(':' . $param, $value, $message);
        foreach($this->_routes as $name => &$route)
        {
            if (!is_object($route))
            {
                list($class, $config, $properties) = Core::getRecords($route);
                $route = $class::install($config, $properties, $this->owner);
            }
            $route->write($level, $message, date($this->datetimeTemplate));
        }
        unset($route);
        return $this;
    }
}
