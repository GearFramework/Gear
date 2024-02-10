<?php

namespace Gear\Plugins\Http;

use Gear\Entities\Http\RequestData;
use Gear\Interfaces\Http\HttpInterface;
use Gear\Interfaces\Http\RequestInterface;
use Gear\Library\Services\Plugin;

/**
 * Плагин для работы с запросами извне
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Request extends Plugin implements RequestInterface, HttpInterface
{
    /* Traits */
    /* Const */
    /* Private */
    private string $defaultRequestMethod = self::GET;
    private ?array $cli = null;
    private array $items = [];
    /* Protected */
    /* Public */

    public function getUri(): string
    {
        return $this->owner->server->requestUri;
    }

    /**
     * Возвращает метод запроса
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        if (php_sapi_name() === 'cli') {
            return self::CLI;
        }
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return $this->defaultRequestMethod;
    }

    /**
     * Возвращает адрес клиента, пославшего запрос
     *
     * @return string
     */
    public function getRemoteAddress(): string
    {
        if (empty($_SERVER['HTTP_CLIENT_IP']) === false) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (empty($_SERVER['HTTP_X_FORWARDED_FOR']) === false) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Установка или получение значений из глобальных массивов
     * $_GET, $_POST, $_REQUEST, $_SESSION, $_FILES, $_COOKIE
     *
     * @param array $data
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getData(array $data, string $name = '', mixed $default = null): mixed
    {
        if ($name === '') {
            return new RequestData($data, $this);
        }
        return $data[$name] ?? $default;
    }

    /**
     * Обработка cookie
     *
     * @param string $name
     * @param array ...$params
     * @return mixed
     */
    public function cookie(string $name = '', ...$params): mixed
    {
        if ($name === '') {
            return $this->getData($_COOKIE);
        }
        if (count($params) > 0) {
            setcookie($name, ...$params);
            return $this;
        }
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Обработка и получение параметров командой строки
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function cli(string $name = '', mixed $default = null): mixed
    {
        $cli = $this->getCliData();
        if ($name) {
            return $cli[$name] ?? $default;
        }
        return new RequestData($cli, $this);
    }

    /**
     * Возвращает массив параметров, переданных в командной
     * строке
     *
     * @return array
     */
    public function getCliData(): array
    {
        if ($this->cli === null) {
            $this->cli = [];
            $short = '';
            $long = [];
            foreach ($_SERVER['argv'] as $value) {
                preg_match('/^(-{1,2})[a-z]+/', $value, $matches);
                if ($matches) {
                    if ($matches[1] == '-')
                        $short .= substr($value, 1) . ':';
                    else
                        $matches[] = substr($value, 2) . ':';
                }
            }
            $this->cli = getopt($short, $long);
        }
        return $this->cli;
    }

    /**
     * Работа с общими параметрами из массива $_REQUEST
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function request(string $name = '', mixed $default = null): mixed
    {
        if ($this->isCli()) {
            return $this->cli($name);
        }
        return $this->getData($_REQUEST, $name, $default);
    }

    /**
     * Работа с параметрами GET запроса
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name = '', mixed $default = null): mixed
    {
        if ($this->isCli()) {
            return $this->cli($name);
        }
        return $this->getData($_GET, $name, $default);
    }

    /**
     * Работа с параметрами GET запроса
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function post(string $name = '', mixed $default = null): mixed
    {
        if ($this->isCli()) {
            return $this->cli($name);
        }
        return $this->getData($_POST, $name, $default);
    }

    /**
     * Работа с параметрами PHP сессий
     *
     * @param string $name
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    public function session(string $name = '', mixed $value = null, mixed $default = null): mixed
    {
        if ($name && $value) {
            $_SESSION[$name] = $value;
            return $this;
        }
        return $this->getData($_SESSION, $name, $default);
    }

    /**
     * Возвращает true, если был сделан ajax-запрос
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Возвращает true, если указанный метод соответствует методу запроса
     *
     * @param string $requestMethod
     * @return bool
     */
    public function isRequestMethod(string $requestMethod): bool
    {
        return $this->getRequestMethod() === strtoupper($requestMethod);
    }

    /**
     * Возвращает true, если запрос сделан из консоли
     *
     * @return bool
     */
    public function isCli(): bool
    {
        return $this->isRequestMethod(self::CLI);
    }

    /**
     * Возвращает true, если сделан GET-запрос
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->isRequestMethod(self::GET);
    }

    /**
     * Возвращает true, если сделан POST-запрос
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->isRequestMethod(self::POST);
    }

    /**
     * Возвращает true, если сделан PUT-запрос
     *
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->isRequestMethod(self::PUT);
    }

    /**
     * Возвращает true, если сделан DELETE-запрос
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->isRequestMethod(self::DELETE);
    }

    /**
     * Возвращает true, если используется обычный http-протокол
     *
     * @return bool
     */
    public function isHttp(): bool
    {
        return $this->isHttps() === false && $this->isCli() === false;
    }

    /**
     * Возвращает true, если используется https
     *
     * @return bool
     */
    public function isHttps(): bool
    {
        return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on';
    }
}
