<?php

namespace gear\library;
use gear\Core;
use gear\library\GObject;
use gear\library\GException;

abstract class GIo extends GObject
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_handler = null;
    /* Public */

    abstract public function open();

    abstract public function read();

    abstract public function write();

    abstract public function close();
}

class IoException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
