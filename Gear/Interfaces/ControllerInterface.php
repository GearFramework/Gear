<?php

namespace Gear\Interfaces;

use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Interfaces\Http\ServerInterface;
use Gear\Interfaces\Objects\ModelInterface;

/**
 * Интерфейс контроллеров
 *
 * @property string             $defaultActionName
 * @property RequestInterface   $request
 * @property ResponseInterface  $response
 * @property ServerInterface    $server
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ControllerInterface extends ModelInterface
{
    /**
     * Вызов экшена контроллера
     *
     * @param   string            $actionRoute
     * @param   ResponseInterface $response
     * @return  mixed
     */
    public function invoke(string $actionRoute, ResponseInterface $response): mixed;

    /**
     * Возвращает название экшена, вызываемого по-умолчанию
     *
     * @return string
     */
    public function getDefaultActionName(): string;

    public function getRequest(): RequestInterface;

    public function getResponse(): ResponseInterface;

    public function getServer(): ServerInterface;
}
