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
        if (Core::app()->request->isAjax()) {
            if (is_string($data)) {
                $data = json_encode(['data-content' => $data, 'errors' => 0]);
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
