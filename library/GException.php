<?php

namespace gear\library;

/** 
 * Класс исключений
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GException extends \Exception
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_args = array();
    /* Public */
    
    /**
     * Конструктор исключения
     * $args = Array
     * (
     *      [argName1] => value1,
     *      [argName2] => value2,
     *      ...
     * )
     * 
     * @access public
     * @param string $message
     * @param array $args
     * @return void
     */
    public function __construct($message, $args = array())
    {
        foreach($args as $name => $value)
        {
            $this->$name = $value;
            $message = str_replace(':' . $name, $value, $message);
        }
        parent::__construct($message);
    }
    
    /**
     * Установка аргумента исключения
     * 
     * @access public
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_args[$name] = $value;
    }
    
    /**
     * Доступ к аргументам исключения
     * 
     * @access public
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_args[$name]) ? $this->_args[$name] : null;
    }
}
