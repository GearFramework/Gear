<?php

namespace Gear\Interfaces\Http;

use Gear\Interfaces\ApplicationInterface;
use Gear\Interfaces\Services\PluginInterface;

/**
 * Интерфейс плагина для работы с запросами
 *
 * @property ApplicationInterface owner
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface RequestInterface extends PluginInterface
{
    /**
     * Работа с общими параметрами из массива $_REQUEST
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function request(string $name = '', mixed $default = null): mixed;

    /**
     * Работа с параметрами GET запроса
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name = '', mixed $default = null): mixed;

    /**
     * Работа с параметрами GET запроса
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function post(string $name = '', mixed $default = null): mixed;

    /**
     * Возвращает метод запроса
     *
     * @return string
     */
    public function getRequestMethod(): string;
}
