<?php

namespace Gear\Traits\Http;

use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Interfaces\Http\ServerInterface;

/**
 * Общий трейт для классов связанных с HTTP
 *
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
trait HttpTrait
{
    protected RequestInterface $request;
    protected ResponseInterface $response;
    protected ServerInterface $server;

    /**
     * Возвращает инстанс запроса
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Устанавливает инстанс запроса
     *
     * @param   RequestInterface $request
     * @return  void
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Возвращает инстанс ответа
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Устанавливает инстанс ответа
     *
     * @param   ResponseInterface $response
     * @return  void
     */
    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    /**
     * Возвращает инстанс обработчика глобальной переменной $_SERVER
     *
     * @return ServerInterface
     */
    public function getServer(): ServerInterface
    {
        return $this->server;
    }

    /**
     * Устанавливает инстанс обработчика глобальной переменной $_SERVER
     *
     * @param   ServerInterface $server
     * @return  void
     */
    public function setServer(ServerInterface $server): void
    {
        $this->server = $server;
    }
}
