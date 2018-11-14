<?php

namespace Gear\Plugins\Http;

use Gear\Core;
use Gear\Interfaces\IResponse;
use Gear\Library\GPlugin;
use Gear\Traits\Http\TMessage;
use Gear\Traits\Http\TResponse;

/**
 * Плагин для работы с ответами на запросы пользователей
 *
 * @package Gear Framework
 *
 * @property array headers
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GResponse extends GPlugin implements IResponse
{
    /* Traits */
    use TMessage;
    use TResponse;
    /* Const */
    /* Private */
    /* Protected */
    protected $_headers = [
        'HTTP/1.0 200 OK',
        'Content-Type' => 'text/html',
    ];
    protected static $_isInitialized = false;
    /* Public */

    /**
     * Отправка заголовка-ответа с указанным статусом
     *
     * @param $code
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sendStatus($code)
    {
        if (!isset(self::$_phrases[$code])) {
            $code = 306;
        }
        header("HTTP/1.0 $code " . isset(self::$_phrases[$code]));
        die();
    }

    /**
     * Отправляет клиенту данные
     *
     * @param mixed $data
     * @return mixed
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function send($data)
    {
        if (is_string($data) && preg_match('#^HTTP/\d\.\d (\d+)#', $data, $math)) {
            if (Core::app()->request->isAjax()) {
                $data = ['error' => $math[1]];
            } else {
                header($data, true, $math[1]);
                return;
            }
        }
        if (Core::app()->request->isAjax()) {
            if (is_string($data)) {
                $data = json_encode(['data-content' => $data, 'error' => 0]);
            } else if (is_array($data)) {
                $data = json_encode($data);
            } else {
                header('HTTP/1.0 200 OK', true, 200);
                return;
            }
        } else {
            if (is_array($data)) {
                $data = json_encode($data);
            } else if (!is_string($data)) {
                header('HTTP/1.0 200 OK', true, 200);
                return;
            }
        }
        foreach ($this->headers as $header => $value) {
            if (is_numeric($header)) {
                header($value);
            } else {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                header("$header: $value");
            }
        }
        echo $data;
    }
}
