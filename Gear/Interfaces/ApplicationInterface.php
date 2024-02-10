<?php

namespace Gear\Interfaces;

use Gear\Components\DeviceDetect\Interfaces\DeviceDetectComponentInterface;
use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Interfaces\Http\ServerInterface;
use Gear\Interfaces\Services\ModuleInterface;

/**
 * Интерфейс приложений
 *
 * @property DeviceDetectComponentInterface $device
 * @property ResponseInterface              $response
 * @property RequestInterface               $request
 * @property RouterInterface                $router
 * @property ServerInterface                $server
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 *
 * @property string $namespace
 */
interface ApplicationInterface extends ModuleInterface
{
    /**
     * Запуск приложения
     * Возвращает код ошибки или 0 если ошибок нет
     *
     * @return int
     */
    public function run(): int;
}
