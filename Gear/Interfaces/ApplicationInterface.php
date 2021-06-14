<?php

namespace Gear\Interfaces;

/**
 * Интерфейс приложений
 *
 * @package Gear Framework
 *
 * @property Gear\Components\DeviceDetect\Interfaces\DeviceDetectComponentInterface device
 * @property RequestInterface request
 * @property ResponseInterface response
 * @property RouterInterface router
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ApplicationInterface
{
    /**
     * Завершение работы приложения
     *
     * @param mixed $result
     * @return void
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function end($result);

    /**
     * Запуск приложения
     *
     * @return void
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function run();
}
