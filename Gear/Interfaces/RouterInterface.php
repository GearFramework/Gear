<?php

namespace Gear\Interfaces;

use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Interfaces\Http\ServerInterface;
use Gear\Interfaces\Services\ComponentInterface;

/**
 * Интерфейс компонентов роутинга запросов
 *
 * @property ControllerInterface    $controller
 * @property RequestInterface       $request
 * @property ResponseInterface      $response
 * @property array                  $routes
 * @property ServerInterface        $server
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface RouterInterface extends ComponentInterface
{
    /**
     * Установка обработчика GET-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function get(string $route, callable $handler): void;

    /**
     * Установка обработчика POST-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function post(string $route, callable $handler): void;

    /**
     * Установка обработчика PUT-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function put(string $route, callable $handler): void;

    /**
     * Установка обработчика DELETE-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function delete(string $route, callable $handler): void;

    /**
     * Установка обработчика указанного роута и метода запроса
     *
     * @param   string            $method
     * @param   string            $route
     * @param   string|callable   $handler
     * @return  void
     */
    public function addRoute(string $method, string $route, string|callable $handler): void;

    /**
     * Получение роута и запуск соответствующего экшена
     *
     * @param   ApplicationInterface $application
     * @return  callable
     */
    public function getAction(ApplicationInterface $application): callable;
}
