<?php

namespace Gear\Plugins\Http;

use Gear\Core;
use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Library\Services\Plugin;
use Gear\Traits\Http\ResponseTrait;

/**
 * Плагин для работы с ответами на запросы пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Response extends Plugin implements ResponseInterface
{
    /* Traits */
    use ResponseTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected array $headers = [
        'Content-Type' => 'text/html;charset=UTF-8',
    ];
    /* Public */

    public function getRequest(): RequestInterface
    {
        return Core::app()->request;
    }

    /**
     * Отправка данных клиенту
     *
     * @param mixed $data
     * @param array $headers
     * @return void
     */
    public function send(mixed $data, array $headers = []): void
    {
        if (is_numeric($data) && isset(self::HTTP_STATUS_PHRASES[(int)$data])) {
            $phrase = self::HTTP_STATUS_PHRASES[(int)$data];
            header("HTTP/1.1 {$data} {$phrase}");
            return;
        }
        $this->headers = array_merge($this->headers, $headers);
        $request = $this->getRequest();
        if ($request->isAjax()) {
            $this->sendAjax($data, $headers);
            return;
        }
        if (is_string($data)
            && preg_match('#^HTTP/\d\.\d (\d+)#', $data, $math)
        ) {
            header($data, true, $math[1]);
            return;
        }
//            if ($data instanceof DirectoryInterface) {
//                return $this->sendDirectory($data, $headers);
//            } elseif ($data instanceof FileInterface) {
//                return $this->sendFile($data, $headers);
//            } else
        if (is_array($data)) {
            $data = json_encode($data);
        } else if (is_string($data) === false) {
            $this->sendStatus(200);
            return;
        }
        $this->sendStatus(200);
        $this->sendHeaders();
        echo $data;
    }

    public function sendAjax(mixed $data, array $headers = []): void
    {
        if (is_string($data)
            && preg_match('#^HTTP/\d\.\d (\d+)#', $data, $math)
        ) {
            $this->send(['error' => $math[1]]);
            return;
        }
        if (is_array($data)) {
            //TODO:set&send application/json
            echo json_encode($data);
            return;
        }
        if (is_string($data) === false) {
            $this->sendStatus(200);
            return;
        }
    }

    /**
     * Отправка установленных заголовков
     *
     * @return ResponseInterface
     */
    public function sendHeaders(): ResponseInterface
    {
        foreach ($this->headers as $header => $value) {
            if (is_numeric($header)) {
                header($value);
                continue;
            }
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            header("$header: $value");
        }
        return $this;
    }

    /**
     * Отправка заголовка-ответа с указанным статусом
     *
     * @param int $code
     * @return ResponseInterface
     */
    public function sendStatus(int $code): ResponseInterface
    {
        if (isset(self::HTTP_STATUS_PHRASES[$code]) === false) {
            $code = self::DEFAULT_STATUS_CODE;
        }
        header("HTTP/1.1 {$code} " . self::HTTP_STATUS_PHRASES[$code], true, $code);
        return $this;
    }
}
