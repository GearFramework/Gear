<?php

namespace gear\components\gear\syslog;
use gear\Core;
use gear\library\GComponent;
use gear\library\GException;

/**
 * Системное протоколирование доступное через \gear\Core::syslog()
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 12.05.2014
 * @see \gear\Core::syslog()
 * @php 5.3.x
 */
class GSyslog extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'plugins' => array
        (
            /* Плагин записи данных протоколирования в файлы */
            'fileLog' => array
            (
                'class' => '\gear\plugins\gear\loggers\GFileLogger',
                'location' => 'logs',
                'templateFilename' => '%Y-%m-%d.log',
                'levels' => array(Core::DEBUG, Core::CRITICAL, Core::WARNING, Core::ERROR),
                'maxLogFileSize' => '10MB',
            ),
        ),
    );
    protected static $_init = false;
    protected $_properties = array
    (
        'datetimeTemplate' => 'd/m/Y H:i:s',
    );
    /* Пути сохранения данных протоколирования */
    protected $_routes = array('fileLog');
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
        foreach($this->routes as $route)
            $this->p($route)->write($level, $message, date($this->datetimeTemplate));
        return $this;
    }
}

/**
 * Класс исключений компонента протоколирования
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 12.05.2014
 */
class SyslogException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
