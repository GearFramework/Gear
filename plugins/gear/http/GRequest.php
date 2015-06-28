<?php

namespace gear\plugins\gear\http;

use gear\Core;
use gear\library\GPlugin;
use gear\library\GException;

define('GET', 'get');
define('POST', 'post');
define('PUT', 'put');
define('DELETE', 'delete');

/**
 * Класс плагина, предоставляющего доступ к данным GET, POST, SESSION, COOKIE
 * 
 * @package Gear Framework
 * @plugin Request
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.3.x
 * @release 1.0.0
 */
class GRequest extends GPlugin
{
    /* Const */
    const GET = 'get';
    const POST = 'post';
    const PUT = 'put';
    const DELETE = 'delete';
    /* Private */
    /* Protected */
    protected static $_config = array('dependency' => '\gear\library\GApplication');
    protected static $_init = false;
    protected $_cliEnvironment = null;
    protected $_filters = array();
    protected $_requestHandlers = array();
    /* Public */
    
    /**
     * Неявный вызов метода request()
     * 
     * @access public
     * @return mixed
     */
    public function __invoke()
    {
        $requestMethod = $this->is();
        return call_user_func_array(array($this, method_exists($this, $requestMethod) ? $requestMethod : 'request'), func_get_args());
    }
    
    /**
     * Возвращает тип запроса
     * 
     * @access public
     * @return integer
     */
    public function is() { return strtolower($_SERVER['REQUEST_METHOD']); }
    
    /**
     * Возвращает true, если тип запроса был GET иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isGet() { return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET'; }
    
    /**
     * Возвращает true, если тип запроса был POST иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isPost() { return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'; }

    /**
     * Возвращает true, если тип запроса был PUT иначе false
     *
     * @access public
     * @return boolean
     */
    public function isPut() { return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'PUT'; }

    /**
     * Возвращает true, если тип запроса был DELETE иначе false
     *
     * @access public
     * @return boolean
     */
    public function isDelete() { return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'DELETE'; }

    /**
     * Установка фильтров
     * 
     * @access public
     * @param array $filters
     * @return void
     */
    public function setFilters(array $filters) { $this->_filters = $filters; }
    
    /**
     * Получение списка фильтров
     * 
     * @access public
     * @return array
     */
    public function getFilters() { return $this->_filters; }

    /**
     * Установка нестандартного обработчика запроса
     * $handlers должен быть массивом вида
     *
     * Array(
     *     self::GET => function() {},
     *     self::POST => array(myObject, 'handlerMethod')
     * )
     *
     * @access public
     * @param array $handlers
     * @return $this
     */
    public function setRequestHandlers(array $handlers) { $this->_requestHandlers = $handlers; }

    /**
     * Установка нестандартного обработчика запроса
     *
     * @access public
     * @param integer $requestType
     * @param string|array|object|\Closure $handler
     * @return $this
     */
    public function setRequestHandler($requestType, $handler)
    {
        if (!is_callable($handler))
            $this->e('Request handler is invalid');
        $this->_requestHandlers[$requestType] = $handler;
        return $this;
    }

    /**
     * Возвращает массив обработчиков http-запросов
     *
     * @access public
     * @return array
     */
    public function getRequestHandlers() { return $this->_requestHandlers; }

    /**
     * Возвращает обработчик указанного метода http-запроса
     *
     * @access public
     * @param integer $requestType (self::GET or self::POST or self::PUT or self::DELETE)
     * @return null|string|array|object|\Closure $handler
     */
    public function getRequestHandler($requestType)
    {
        return isset($this->_requestHandlers[$requestType]) ? $this->_requestHandlers[$requestType] : null;
    }

    /**
     * Возвращает true, если существует пользовательский обработчик указанного метода запроса
     *
     * @access public
     * @param string $requestType
     * @return bool
     */
    public function isRequestHandler($requestType) { return isset($this->_requestHandlers[$requestType]); }

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
        if ($this->isRequestHandler(self::GET))
        {
            $handler = $this->getRequestHandler(self::GET);
            return $handler($name, $default, $filter);
        }
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
        if ($this->isRequestHandler(self::POST))
        {
            $handler = $this->getRequestHandler(self::POST);
            return $handler($name, $default, $filter);
        }
        return $this->_data($_POST, $name, $default, $filter);
    }

    /**
     * Обработка запроса PUT
     *
     * @access public
     * @return mixed
     */
    public function put()
    {
        if ($this->isRequestHandler(self::PUT))
        {
            $handler = $this->getRequestHandler(self::PUT);
            return call_user_func_array($handler, func_get_args());
        }
        return null;
    }

    /**
     * Обработка запроса DELETE
     *
     * @access public
     * @return mixed
     */
    public function delete()
    {
        if ($this->isRequestHandler(self::DELETE))
        {
            $handler = $this->getRequestHandler(self::DELETE);
            return call_user_func_array($handler, func_get_args());
        }
        return null;
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
    public function request($name = null, $default = null, $filter = null) { return $this->_data($_REQUEST, $name, $default, $filter); }
    
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
     * Получение данных о загруженных файлах
     * 
     * @access public
     * @param string $name
     * @param mixed $filter
     * @return mixed
     */
    public function files($name = null, $filter = null)
    {
        if ($name === null)
            return $_FILES;
        else
        {
            if (!isset($_FILES[$name]))
                return null;
            if (is_array($_FILES[$name]))
            {
                $files = array();
                foreach($_FILES[$name]['name'] as $index => $fileName)
                {
                    $file = 
                    [
                        'name' => $fileName,
                        'type' => $_FILES[$name]['type'][$index],
                        'tmp_name' => $_FILES[$name]['tmp_name'][$index],
                        'error' => $_FILES[$name]['error'][$index],
                        'size' => $_FILES[$name]['size'][$index],
                    ];
                    if (!$filter || ($filter && ($file = $this->filtering($filter, $file))))
                        $files[] = $file;
                }
            }
            else
                return $filter ? $this->filtering($filter, $_FILES[$name]) : $_FILES[$name];
        }
    }

    /**
     * Принудительная установка параметров запроса
     *
     * @access public
     * @param array $request
     * @return $this
     */
    public function setData(array $request)
    {
        if ($this->isGet())
            $_GET = $request;
        else
        if ($this->isPost())
            $_POST = $request;
        return $this;
    }
    
    /**
     * Получение запрошенного значения
     * 
     * @access protected
     * @param array $data
     * @param string $name
     * @param mixed $default
     * @param mixed $filter
     * @return mixed
     */
    protected function _data(array &$data, $name, $default, $filter)
    {
        if ($this->getOwner()->isCli())
            return $this->cli($name, $default, $filter);
        if ($name === null)
            return $data;
        if (is_string($name))
        {
            if (!isset($data[$name]))
                return $default;
            return $filter ? $this->filtering($filter, $data[$name], $default) : $data[$name];
        }
        else
        if (is_array($name))
        {
            /* Arrays is alias of /gear/helpers/GArray */
            if (Arrays::isAssoc($name))
            {
                $data = $name;
                return $this;
            }
            else
            {
                $result = array();
                foreach($name as $dataName)
                {
                    if (isset($data[$dataName]))
                        $result[$dataName] = $filter ? $this->filtering($filter, $data[$dataName], $default) : $data[$dataName];
                    else
                        $result[$dataName] = $default;
                }
                return $result;
            }
        }
        return $default;
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
        if (is_null($this->_cliEnviroment))
            $this->_prepareCli();
        if ($name)
        {
            if (!$name)
                return $this->_cliEnvironment;
            else
            if (isset($this->_cliEnvironment[$name]))
                return $filter ? $this->filtering($filter, $this->_cliEnviroment[$name], $default) : $this->_cliEnvironment[$name];
            else
                return $default;
        }
        else
            return $this->_cliEnvironment;
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
        $this->_cliEnvironment = getopt($short, $long);
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
        if (is_string($filter) && isset($this->_filters[$filter]))
            $value = call_user_func(array($this->_filters[$filter], $filter), $value, $default);
        else
        if (is_callable($filter))
            $value = call_user_func($filter, $value, $default);
        else
        if (is_array($filter))
        {
            foreach($filter as $filterItem)
                $value = $this->filtering($filterItem, $value, $default);
        }
        else
            $this->e('Unknown filter');
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
