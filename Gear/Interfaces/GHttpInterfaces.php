<?php

namespace Gear\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

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
interface GRequestInterface extends ServerRequestInterface
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
     * Возвращает параметры get-запроса или значение указанного параметра
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function get(string $name = '');

    /**
     * Возвращает параметры get-запроса или значение указанного параметра
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function post(string $name = '');

    public function cookie();

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
interface GResponseInterface extends ResponseInterface
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

interface GUriInterface extends UriInterface
{

}
