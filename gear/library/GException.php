<?php

namespace gear\library;

use gear\Core;

/**
 * Класс исключений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GException extends \Exception
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_locationLocales = '\gear\exceptions\locales';
    protected static $_messages = null;
    protected $_section = 'exceptions';
    protected $_args = [];
    /* Public */
    public $defaultMessage = 'Exception';

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
     * @param integer $code
     * @param \Exception $previous
     * @param array $args
     * @return GException
     */
    public function __construct($message, $code = 0, \Exception $previous = null, array $args = [])
    {
        $message = $message !== null ? $message : $this->defaultMessage;
        foreach ($args as $name => $value)
            $this->$name = $value;
        if (class_exists('International', false))
            $message = International::t($message, self::$_locationLocales, $args);
        else {
            if (self::$_messages === null) {
                $locale = Core::params('locale') ?: 'en_En';
                $path = Core::resolvePath(self::$_messages) . '/' . $locale . '.php';
                Core::syslog(get_class($this) . ' -> Load file with messages ' . $path . ' [' . __LINE__ . ']');
                self::$_messages = file_exists($path) && is_readable($path) ? require($path) : [];
            }
            if (isset(self::$_messages[$this->_section][$message]))
                $message = self::$_messages[$this->_section][$message];
            foreach ($this->args as $name => $value) {
                $this->$name = $value;
                $message = str_replace(':' . $name, $value, $message);
            }
        }
        parent::__construct($message, $code, $previous);
        Core::syslog(get_class($this) . ' -> Message ' . $message . ' in file ' . $this->getFile() . ':' . $this->getLine() . ' [' . __LINE__ . ']');
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
     * @param null|string $name
     * @return array
     */
    public function args($name = null)
    {
        return !$name ? $this->_args : (isset($this->_args[$name]) ? $this->_args[$name] : null);
    }
}
