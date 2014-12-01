<?php

namespace gear\library;
use gear\Core;
use gear\library\GObject;
use gear\library\GException;

abstract class GIo extends GObject
{
    /* Const */
    const UNKNOWN = 0;
    const FIFO = 1;
    const CHAR = 2;
    const DIR = 3;
    const BLOCK = 4;
    const LINK = 5;
    const FILE = 6;
    const SOCKET = 7;
    /* Private */
    /* Protected */
    protected $_handler = null;
    protected $_types =
    [
        self::UNKNOWN => 'unknown',
        self::FIFO => 'fifo',
        self::CHAR => 'char',
        self::DIR => 'dir',
        self::BLOCK => 'block',
        self::LINK => 'link',
        self::FILE => 'file',
        self::SOCKET => 'socket',
    ]
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
