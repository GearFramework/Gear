<?php

namespace gear\plugins\gear;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GException;

/** 
 * Класс плагина, предоставляющего доступ к данным GET, POST, SESSION, COOKIE
 * 
 * @package Gear Framework
 * @plugin Request
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GRequest extends GPlugin
{
    /* Const */
    const GET = 1;
    const POST = 2;
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'dependency' => '\\gear\\library\\GApplication',
    );
    protected static $_init = false;
    protected $_cliEnviroment = null;
    protected $_filters = array();
    /* Public */
    
    /**
     * Неявный вызов метода request()
     * 
     * @access public
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array(array($this, 'request'), func_get_args());
    }
    
    /**
     * Возвращает тип запроса
     * 
     * @access public
     * @return integer
     */
    public function is() { return $_SERVER['REQUEST_METHOD'] === 'POST' ? self::POST : self::GET; }
    
    /**
     * Возвращает true, если тип запроса был GET иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isGet() { return $_SERVER['REQUEST_METHOD'] === 'GET'; }
    
    /**
     * Возвращает true, если тип запроса был POST иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isPost() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
    
    /**
     * Установка фильтров
     * 
     * @access public
     * @param array $filters
     * @return void
     */
    public function setFilters(array $filters)
    {
        $this->_filters = $filters;
    }
    
    /**
     * Получение списка фильтров
     * 
     * @access public
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }
    
    /**
     * Получение значения из массива $_GET
     * 
     * @access public
     * @param string $name
     * @param mixed $default
     * @param mixed $filter
     * @return mixed
     */
    public function get($name = null, $default = null, $filter = null)
    {
        return $this->_data($_GET, $name, $default, $filter);
    }
    
    /**
     * Получение значения из массива $_POST
     * 
     * @access public
     * @param string $name
     * @param mixed $default
     * @param mixed $filter
     * @return mixed
     */
    public function post($name = null, $default = null, $filter = null)
    {
        return $this->_data($_POST, $name, $default, $filter);
    }
    
    /**
     * Получение значения из массива $_REQUEST
     * 
     * @access public
     * @param string $name
     * @param mixed $default
     * @param mixed $filter
     * @return mixed
     */
    public function request($name = null, $default = null, $filter = null)
    {
        return $this->_data($_REQUEST, $name, $default, $filter);
    }
    
    /**
     * Получение/установка значений cookie
     * 
     * @access public
     * @return mixed
     */
    public function cookie()
    {
        if (func_num_args() == 1)
            return isset($_COOKIE[func_get_arg(0)]) ? $_COOKIE[func_get_arg(0)] : null;
        else
            return call_user_func_array('setcookie', func_get_args());
    }
    
    /**
     * Получение/установка значений сессии
     * 
     * @access public
     * @param string $name
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    public function session($name = null, $value = null, $default = null)
    {
        if ($name === null)
            return $_SESSION;
        else
        if ($value === null)
            return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
        else
            $_SESSION[$name] = $value;
    }
    
    /**
     * Получение запрошенного значения
     * 
     * @access protected
     * @param array $data
     * @param string $name
     * @param mixed $default
     * @param mixed $filter
     * @return mxied
     */
    protected function _data(array &$data, $name, $default, $filter)
    {
        if ($this->getOwner()->hasCli())
            return $this->cli($name, $default, $filter);
        else
        {
            if ($name === null) 
                return $data;
            if (!isset($data[$name])) 
                return $default;
            return $filter ? $this->filtering($filter, $data[$name], $default) : $data[$name];
        }
    }
    
    /**
     * Получением значения параметра из командной строки
     * 
     * @access public
     * @param string $name
     * @param mixed $default
     * @param mixed $filter
     * @return mixed
     */
    public function cli($name, $default = null, $filter = null)
    {
        if ($name)
        {
            if (is_null($this->_cliEnviroment))
                $this->_prepareCli();
            if (isset($this->_cliEnviroment[$name]))
                return $filter ? $this->filtering($filter, $this->_cliEnviroment[$name], $default) : $this->_cliEnviroment[$name];
                
            else
                return $default;
        }
        else
            return $default;
    }
    
    /**
     * Подготовка массива с параметрами из командной строки
     * 
     * @access protected 
     * @return void
     */
    protected function _prepareCli()
    {
        $short = '';
        $long = array();
        foreach($_SERVER['argv'] as $value)
        {
            preg_match('/^(\-{1,2})[a-z]+/', $value, $res);
            if ($res)
            {
                if ($res[1] == '-')
                    $short .= substr($value, 1) . ':';
                else
                    $long[] = substr($value, 2) . ':';
            }
        }
        $this->_cliEnviroment = getopt($short, $long);
    }
    
    /**
     * Фильтрация значения
     * 
     * @access public
     * @param string $filter
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    public function filtering($filter, $value, $default = null)
    {
        if (is_callable($filter))
            $value = call_user_func($filter, $value, $default);
        else
        if (is_string($filter) && isset($this->_filters[$filter]))
            $value = call_user_func(array($this->_filters[$filter], $filter), $value, $default);
        else
        if (is_array($filter))
        {
            foreach($filter as $filterItem)
                $value = $this->_filtering($filterItem, $value, $default);
        }
        else
            $this->e('Неизвестный фильтр');
        return $value;
    }
}

/** 
 * Класс исключений плагина Request
 * 
 * @package Gear Framework
 * @plugin Request
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class RequestException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
