<?php

namespace gear\plugins\http;

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
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_defaultMethod = self::GET;
    protected $_cli = null;
    protected $_variablesOrder = null;
    protected $_orders = ['G' => 'GET', 'P' => 'POST', 'C' => 'COOKIE', 'S' => 'SESSION'];
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
            $this->_variablesOrder = preg_split('//', ini_get('variables_order'), -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach($this->_variablesOrder as $sym) {
            echo "$name - $sym " . $this->_orders[$sym] . '<br>';
            if (isset($this->_orders[$sym])) {
                $data = '_' . $this->_orders[$sym];
                echo "$data<br>";
                print_r($_GET);
                if (isset($$data[$name])) {
                    echo "Isset $name<br>";
                    $method = strtolower($this->_orders[$sym]);
                    return $this->$method($name);
                }
            }
        }
        return parent::__get($name);
    }

    public function cli(string $name = '', $default = null)
    {
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

    public function get(string $name = '', $default = null)
    {
        if (!$name) {
            $value = new GModel($_GET, $this);
        } else if (isset($_GET[$name])) {
            $value = $_GET[$name];
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

    public function post(string $name = '')
    {
        if (!$name)
            return $_POST;
        // TODO: Implement post() method.
    }

    public function cookie($name = null)
    {
        if (!$name)
            return $_COOKIE;
        // TODO: Implement cookie() method.
    }

    public function session(string $name = '', $value = null)
    {
        if (!$name)
            return $_SESSION;
        // TODO: Implement session() method.
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

    public function uploads(string $name = '')
    {
        // TODO: Implement uploads() method.
    }
}