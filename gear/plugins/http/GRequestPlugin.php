<?php

namespace gear\plugins\http;

use gear\Core;
use gear\interfaces\IRequest;
use gear\library\GModel;
use gear\library\GPlugin;
use gear\traits\http\TServerRequest;

/**
 * Плагин для работы с запросами пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GRequestPlugin extends GPlugin implements IRequest
{
    /* Traits */
    use TServerRequest;
    /* Const */
    const CLI = 'CLI';
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const SESSION = 'SESSION';
    const COOKIE = 'COOKIE';
    const FILES = 'FILES';
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_defaultMethod = self::GET;
    protected $_cli = null;
    protected $_variablesOrder = null;
    protected $_orders = ['G' => 'GET', 'P' => 'POST', 'C' => 'COOKIE', 'S' => 'SESSION', 'CLI' => 'CLI'];
    protected $_requestHandlers = [];
    protected $_files = null;
    /* Public */

    public function __invoke(string $name = '')
    {
        $method = strtolower($this->getMethod());
        if (method_exists($this, $method)) {
            return $this->$method($name);
        }
        return $this;
    }

    public function __get(string $name)
    {
        if (!$this->_variablesOrder) {
            if ($this->isCli()) {
                $this->_variablesOrder = ['CLI'];
            } else {
                $this->_variablesOrder = preg_split('//', ini_get('variables_order'), -1, PREG_SPLIT_NO_EMPTY);
            }
        }
        foreach($this->_variablesOrder as $sym) {
            if (isset($this->_orders[$sym])) {
                $method = strtolower($this->_orders[$sym]);
                $inMethod = 'in' . ucfirst(strtolower($this->_orders[$sym]));
                if (method_exists($this, $inMethod) && $this->$inMethod($name)) {
                    return $this->$method($name);
                }
            }
        }
        return parent::__get($name);
    }

    public function cli(string $name = '', $default = null)
    {
        if ($this->isRequestHandler(self::CLI)) {
            $handler = $this->getRequestHandler(self::CLI);
            return $handler($name);
        }
        if ($this->_cli === null) {
            $short = '';
            $long = [];
            foreach($_SERVER['argv'] as $value) {
                preg_match('/^(\-{1,2})[a-z]+/', $value, $res);
                if ($res) {
                    if ($res[1] == '-')
                        $short .= substr($value, 1) . ':';
                    else
                        $long[] = substr($value, 2) . ':';
                }
            }
            $this->_cli = getopt($short, $long);
        }
        if ($name) {
            $value = $default;
            if (isset($this->_cli[$name])) {
                $value = $this->validate($name, $this->_cli[$name], $default);
            }
        } else {
            $value = new GModel($this->_cli);
        }
        return $value;
    }

    public function cookie(string $name = '')
    {
        if ($this->isRequestHandler(self::COOKIE)) {
            $handler = $this->getRequestHandler(self::COOKIE);
            return $handler($name);
        }
        if (!$name) {
            return $this->getData($_COOKIE);
        }
    }

    public function get(string $name = '', $default = null)
    {
        if ($this->isCli()) {
            return $this->cli($name);
        }
        if ($this->isRequestHandler(self::GET)) {
            $handler = $this->getRequestHandler(self::GET);
            return $handler($name);
        }
        return $this->getData($_GET, $name);
    }

    public function getData(array &$data, string $name = '', $value = null)
    {
        if (!$name) {
            $value = new GModel($data, $this);
        } else if (isset($data[$name]) && $value === null) {
            $value = $data[$name];
        } else if ($name && $value !== null) {
            $data[$name] = $value;
        }
        return $value;
    }

    /**
     * Возвращает метод по-умолчанию
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultMethod(): string
    {
        return $this->_defaultMethod;
    }

    /**
     * Возвращает метод запроса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMethod(): string
    {
        $method = $this->defaultMethod;
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } else if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        return strtoupper($method);
    }

    /**
     * Возвращает обработчик указанного метода запроса
     *
     * @param string $name
     * @return mixed|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRequestHandler(string $name)
    {
        $handler = null;
        $name = strtoupper($name);
        if (isset($this->_requestHandlers[$name])) {
            if (!is_callable($this->_requestHandlers[$name])) {
                if (is_array($this->_requestHandlers[$name])) {
                    list($class,, $properties) = Core::configure($this->_requestHandlers[$name]);
                    $handler = new $class($properties);
                    if (!is_callable($handler)) {
                        throw self::exceptionInvalidRequestHandler(['method' => $name]);
                    }
                    $this->_requestHandlers[$name] = $handler;
                }
            } else {
                $handler = $this->_requestHandlers[$name];
            }
        }
        return $handler;
    }

    /**
     * Возвращает массив установленных обработчиков запросов (GET, POST, PUT, DELETE)
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRequestHandlers(): array
    {
        return $this->_requestHandlers;
    }

    public function getToken(): string
    {
        return '';
    }

    public function inCli(string $name)
    {
    }

    public function inCookie(string $name): bool
    {
        return array_key_exists($name, $_COOKIE);
    }

    public function inGet(string $name): bool
    {
        return array_key_exists($name, $_GET);
    }

    public function inFiles(string $name): bool
    {
        return array_key_exists($name, $_FILES);
    }

    public function inPost(string $name): bool
    {
        return array_key_exists($name, $_POST);
    }

    public function inSession(string $name): bool
    {
        return array_key_exists($name, $_SESSION);
    }

    /**
     * Возвращает true, если был сделан ajax-запрос
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * Возвращает true, если запрос сделан из консоли
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * Возвращает true, если сделан GET-запрос
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isGet(): bool
    {
        return $this->method === self::GET;
    }

    /**
     * Возвращает true, если используется обычный http-протокол
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isHttp(): bool
    {
        return !$this->isHttps() && !$this->isCli();
    }

    /**
     * Возвращает true, если используется https
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isHttps(): bool
    {
        return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on';
    }

    /**
     * Возвращает true, если сделан POST-запрос
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPost(): bool
    {
        return $this->method === self::POST;
    }

    /**
     * Возвращает true, если сущеуствует обработчик указанного запроса
     *
     * @param string $methodName
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isRequestHandler(string $methodName): bool
    {
        return isset($this->_requestHandlers[$methodName]);
    }

    /**
     * Нормализация массив $_FILES
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function normalizeFiles()
    {
        $this->_files = [];
        foreach($_FILES as $uploadName => $section) {
            foreach($section as $sectionName => $values) {
                foreach($values as $index => $value)
                    $this->_files[$uploadName][$index][$sectionName] = $value;
            }
        }
    }

    public function post(string $name = '')
    {
        if ($this->isCli()) {
            return $this->cli($name);
        }
        if ($this->isRequestHandler(self::POST)) {
            $handler = $this->getRequestHandler(self::POST);
            return $handler($name);
        }
        return $this->getData($_POST);
    }

    public function session(string $name = '', $value = null)
    {
        if ($this->isRequestHandler(self::SESSION)) {
            $handler = $this->getRequestHandler(self::SESSION);
            return $handler($name);
        }
        return $this->getData($_SESSION, $name, $value);
    }

    /**
     * Установка метода по-умолчанию
     *
     * @param string $defaultMethod
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDefaultMethod(string $defaultMethod)
    {
        $this->_defaultMethod = $defaultMethod;
    }

    /**
     * Установка массива обработчиков запросов
     *
     * @param array $handlers
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setRequestHandlers(array $handlers)
    {
        $this->_requestHandlers = $handlers;
    }

    public function uploads(string $name = '')
    {
        if ($this->isRequestHandler(self::FILES)) {
            $handler = $this->getRequestHandler(self::FILES);
            return $handler($name);
        }
        if ($this->_files === null) {
            $this->normalizeFiles();
        }
        return $this->getData($this->_files, $name);
    }
}