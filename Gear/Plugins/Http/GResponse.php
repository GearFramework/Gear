<?php

namespace Gear\Plugins\Http;

use Gear\Core;
use Gear\Interfaces\ResponseInterface;
use Gear\Library\GPlugin;
use Gear\Traits\Http\MessageTrait;
use Gear\Traits\Http\ResponseTrait;

/**
 * Плагин для работы с ответами на запросы пользователей
 *
 * @package Gear Framework
 *
 * @property array headers
 * @property string protocolVersion
 * @property string reasonPhrase
 * @property int statusCode
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GResponse extends GPlugin implements ResponseInterface
{
    /* Traits */
    use MessageTrait;
    use ResponseTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_headers = [
        'Content-Type' => 'text/html',
    ];
    protected static $_isInitialized = false;
    /* Public */

    /**
     * Отправка заголовка-ответа с указанным статусом
     *
     * @param $code
     * @return GResponse
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sendStatus($code): GResponse
    {
        if (!isset(self::$_phrases[$code])) {
            $code = 306;
        }
        header("HTTP/" . $this->protocolVersion . " $code " . isset(self::$_phrases[$code]), true, $code);
        return $this;
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
            if (is_array($data)) {
                $data = json_encode($data);
            } elseif (!is_string($data)) {
                $this->sendStatus(200);
                return;
            }
        } else {
            if (is_array($data)) {
                $data = json_encode($data);
            } else if (!is_string($data)) {
                $this->sendStatus(200);
                return;
            }
        }
        $this->sendStatus(200);
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
