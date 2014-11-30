<?php

namespace gear\library;
use gear\Core;
use gear\library\GIo;
use gear\library\GException;

abstract class GFileSystem extends GIo
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public function path() { return $this->path; }
    
    public function basename() { return basename($this->path); }
    
    public function name() { return pathinfo($this->path, PATHINFO_FILENAME); }
    
    public function ext() { return $this->extencion(); }
    
    public function extension() { return pathinfo($this->path, PATHINFO_EXTENSION); }
    
    abstract public function size();

    abstract public function copy();
    
    abstract public function rename();
    
    abstract public function remove();
}

class FileSystemException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
