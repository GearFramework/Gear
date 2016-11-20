<?php

namespace gear\plugins\http;

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
    }
}
