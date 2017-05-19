<?php

namespace gear\plugins\http;

use gear\Core;
use gear\interfaces\IResponse;
use gear\library\GPlugin;
use gear\traits\http\TMessage;
use gear\traits\http\TResponse;


class GResponsePlugin extends GPlugin implements IResponse
{
    /* Traits */
    use TMessage;
    use TResponse;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    /* Public */

    public function __invoke()
    {
        return $this;
    }

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
                die();
            }
        }
        if (Core::app()->request->isAjax()) {
            if (is_string($data)) {
                $data = json_encode(['data-content' => $data, 'error' => 0]);
            } else if (is_array($data)) {
                $data = json_encode($data);
            } else {
                header('HTTP/1.0 200 OK', true, 200);
                die();
            }
        } else {
            if (is_array($data)) {
                $data = json_encode($data);
            } else if (!is_string($data)) {
                header('HTTP/1.0 200 OK', true, 200);
                die();
            }
        }
        echo $data;
    }
}
