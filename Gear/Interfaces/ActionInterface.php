<?php

namespace Gear\Interfaces;

use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Interfaces\Objects\ModelInterface;

/**
 * Интерфейс экшена, который будет выполнен по соответствующему роуту
 *
 * @property RequestInterface   $request
 * @property ResponseInterface  $response
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ActionInterface extends ModelInterface
{
    /**
     * Вызов экшена
     *
     * @param   RequestInterface  $request
     * @param   ResponseInterface $response
     * @return  mixed
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): mixed;

    /**
     * Вызов экшена
     *
     * @param   RequestInterface  $request
     * @param   ResponseInterface $response
     * @return  mixed
     */
    public function invoke(RequestInterface $request, ResponseInterface $response): mixed;
}
