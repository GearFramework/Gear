<?php

namespace gear\plugins\http;

use gear\interfaces\IRequest;
use gear\library\GPlugin;
use gear\traits\http\TServerRequest;

class GRequestPlugin extends GPlugin implements IRequest
{
    /* Traits */
    use TServerRequest;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    /* Public */

    public function __invoke(string $name = '')
    {
        // TODO: Implement __invoke() method.
    }

    public function get(string $name = '')
    {
        // TODO: Implement get() method.
    }

    public function post(string $name = '')
    {
        // TODO: Implement post() method.
    }

    public function cookie()
    {
        // TODO: Implement cookie() method.
    }

    public function session(string $name = '', $value = null)
    {
        // TODO: Implement session() method.
    }

    public function uploads(string $name = '')
    {
        // TODO: Implement uploads() method.
    }
}