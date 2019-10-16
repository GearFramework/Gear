<?php

namespace Gear\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Интерфейс плагина для работы с запросами
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface RequestInterface extends ServerRequestInterface
{
    /**
     * Возвращает параметры текущего запроса (GET или POST)
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke(string $name = '');

    /**
     * Обработка и получение параметров командой строки
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function cli(string $name = '', $default = null);

    /**
     * Обработка cookie
     *
     * @param string $name
     * @param array ...$params
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function cookie(string $name = '', ...$params);

    /**
     * Возвращает параметры get-запроса или значение указанного параметра
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function get(string $name = '', $default = null);

    /**
     * Возвращает метод по-умолчанию
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultMethod(): string;

    /**
     * Возвращает нормализованный список загруженных файлов на сервер
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getFiles(): array;

    /**
     * Возвращает метод запроса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMethod(): string;

    /**
     * Возвращает адрес клиента, пославшего запрос
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRemoteAddress(): string;

    /**
     * Возвращает true, если был сделан ajax-запрос
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isAjax(): bool;

    /**
     * Возвращает значение указанного параметра, независимо от метода запроса
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function param(string $name, $default = null);

    /**
     * Работа с параметрами GET запроса
     *
     * @param string $name
     * @param null $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function post(string $name = '', $default = null);

    /**
     * Возвращает параметры cессии
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function session(string $name = '', $value = null);

    /**
     * Возвращает загруженные файлы
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uploads(string $name = '');
}

/**
 * Интерфейс плагина для работы с ответами на пользовательские запросы
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ResponseInterface extends \Psr\Http\Message\ResponseInterface
{
    /**
     * Отправляет клиенту данные
     *
     * @param mixed $data
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function send($data);
}

interface UriInterface extends \Psr\Http\Message\UriInterface
{

}
