<?php

namespace gear\library;
use gear\Core;
use gear\library\GFileSystem;
use gear\library\GException;

class GFolder extends GFileSystem implements \Iterator
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_current = null;
    protected $_filter = '*';
    /* Public */
    
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }
    
    public function getFilter() { return $this->_filter; }
    
    public function current() { return $this->_current; }
    
    public function key() { return $this->_current->path; }
    
    public function next()
    {
        if (!$file = $this->read())
            $this->_current = null;
    }
    
    public function rewind()
    {
        if ($this->_handler)
            $this->close();
        $this->open(); 
    }
    
    public function valid() { return is_object($this->_current); }
    
    public function open() { $this->_handler = opendir($this->path); }
    
    public function close()
    {
        closedir($this->_handler);
        $this->_handler = null;
        return $this;
    }
    
    public function glob($filer = '*')
    {
        $this->filter = $filer;
        return $this;
    }
    
    public function read()
    {
        try
        {
            return ($file = readdir($this->_handler)) 
                   ? $this->_current = $this->factory(['path' => $this->path . '/' . $file]) 
                   : $this->_current = null;
        }
        catch(\Exception $e)
        {
            return $this->_cirrent = null;
        }
    }
}

class FolderException  extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
