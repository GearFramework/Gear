<?php

namespace gear\library;
use gear\Core;
use gear\library\GFileSystem;
use gear\library\GException;

class GFile extends GFileSystem
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public function size($format = null)
    {
        return filesize($this->path);
    }
}

class FileException  extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
