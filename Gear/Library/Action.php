<?php

namespace Gear\Library;

use Gear\Interfaces\ActionInterface;
use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Library\Objects\Model;

/**
 * Экшен соответствующий роуту
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
abstract class Action extends Model implements ActionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    private RequestInterface $request;
    private ResponseInterface $response;
    /* Protected */
    /* Public */

    /**
     * Вызов экшена
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): mixed
    {
        return $this->invoke($request, $response);
    }

    /**
     * Вызов экшена
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @return mixed
     */
    abstract public function invoke(RequestInterface $request, ResponseInterface $response): mixed;
}
