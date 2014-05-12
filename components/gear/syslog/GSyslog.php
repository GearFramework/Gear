<?php

namespace gear\components\gear\syslog;
use gear\Core;
use gear\library\GComponent;
use gear\library\GException;

/**
 * Системное протоколирование
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 12.05.2014
 */
class GSyslog extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = array();
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
        return $this;
    }
}

/**
 * Класс исключений компонента протоколирования
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 12.05.2014
 */
class SyslogException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
