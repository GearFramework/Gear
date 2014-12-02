<?php

namespace gear\library;
use gear\Core;
use gear\library\GFileSystem;
use gear\library\GException;

/**
 * Директория
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
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
    
    /**
     * Следующий элемент в папке
     * 
     * @access public
     * @return void
     */
    public function next() { $this->read(); }
    
    /**
     * Перемотка на первый элемент в папке
     * 
     * @access public
     * @return void
     */
    public function rewind()
    {
        if ($this->_handler)
            $this->close();
        $this->open();
        $this->read();
    }
    
    /**
     * Возвращает true если текущий элемент не является NULL
     * 
     * @access public
     * @return boolean
     */
    public function valid() { return is_object($this->_current); }
    
    /**
     * Открывает папку для чтения
     * 
     * @access public
     * @return $this
     */
    public function open() 
    { 
        if ($this->_handler)
            $this->close();
        $this->_handler = @opendir($this->path);
        if (!$this->_handler)
            $this->e('Cannot open file :fileName', ['fileName' => $this->path]);
        return $this; 
    }
    
    /**
     * Чтение элементов из папки
     * 
     * @access public
     * @return null|object
     */
    public function read()
    {
        $this->_current = null;
        try
        {
            if (($file = readdir($this->_handler)) !== false)
            {
                
                $this->_current = $this->factory(['path' => $this->path . '/' . $file]);
            }
        }
        catch(\Exception $e)
        {
        }
        return $this->_current;
    }
    
    public function write() {}
    
    /**
     * Закрывает папку
     * 
     * @access public
     * @return $this
     */
    public function close()
    {
        if ($this->_handler)
        {
            closedir($this->_handler);
            $this->_handler = null;
        }
        return $this;
    }
    
    public function glob($filer = '*')
    {
        $this->filter = $filer;
        return $this;
    }
    
    /**
     * Возвращает размер элемента в байтах
     * 
     * @access public
     * @return integer
     */
    public function getSize()
    {
        return 0;
    }
    
    /**
     * Копирует папку в указанное место
     * 
     * @access public
     * @param string|object $dest
     * @return object
     */
    public function copy($dest)
    {
        
    }
    
    /**
     * Переименование/перемещение папки со всем содержимым
     * 
     * @access public
     * @param string|object $dest
     * @return object
     */
    public function rename($dest)
    {
        
    }
    
    /**
     * Удаление папки со всем содержимым
     * 
     * @access public
     * @return null
     */
    public function remove()
    {
        
    }
}

/**
 * Исключения директорий
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
class FolderException  extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
