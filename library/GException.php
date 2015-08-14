<?php

namespace gear\library;

/**
 * Класс исключений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.3.x
 * @release 1.0.0
 */
class GException extends \Exception
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_args = array();
    /* Public */
    public $defaultMessage = '';

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
     * @return GException
     */
    public function __construct($message, $code = 0, \Exception $previous = null, array $args = array())
    {
        $message = $message !== null ?: $this->defaultMessage;
        foreach($args as $name => $value)
        {
            $this->$name = $value;
            $message = str_replace(':' . $name, $value, $message);
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * Установка аргумента исключения
     *
     * @access public
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value) { $this->_args[$name] = $value; }

    /**
     * Доступ к аргументам исключения
     *
     * @access public
     * @param string $name
     * @return mixed
     */
    public function __get($name) { return isset($this->_args[$name]) ? $this->_args[$name] : null; }

    /**
     * Возвращает массив аргументов
     *
     * @access public
     * @return array
     */
    public function args($name = null) { return !$name ? $this->_args : (isset($this->_args[$name]) ? $this->_args[$name] : null); }
}
