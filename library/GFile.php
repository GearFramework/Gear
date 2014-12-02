<?php

namespace gear\library;
use gear\Core;
use gear\library\GFileSystem;
use gear\library\GException;

/**
 * Файл
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
class GFile extends GFileSystem
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    /**
     * Открытие файла
     * 
     * @access public
     * @param string $mode
     * @param boolean $useIncludePath
     * @param null|resource $context
     * @return $this
     */
    public function open($mode = 'r', $useIncludePath = false, $context = null)
    {
        if ($this->_handler)
            $this->close();
        $this->_handler = @fopen($mode, $useIncludePath, $context);
        if (!$this->_handler)
            $this->e('Cannot open file :fileName', ['fileName' => $this->path]);
        return $this;
    }
    
    /**
     * Чтение из файла
     * 
     * @access public
     * @param null|integer $length
     * @return mixed
     */
    public function read($length = null)
    {
        if (!$this->_handler)
            $this->open();
        return fread($this->_handler, $length);
    }
    
    /**
     * Запись в файл
     * 
     * @access public
     * @param integer|string|object $data
     * @return 
     */
    public function write($data = '')
    {
        if (!$this->_handler)
            $this->open('a+');
        if (is_array($data) || is_object($data))
            $data = serialize($data);
        return fwrite($this->_handler, $data);
    }
    
    /**
     * Закрытие файла
     * 
     * @access public
     * @return $this
     */
    public function close()
    {
        if ($this->_handler)
            fclose($this->_handler);
        return $this;
    }
    
    /**
     * Возвращает размер элемента в байтах
     * 
     * @access public
     * @return integer
     */
    public function getSize() { return (int)filesize($this->path()); }
    
    /**
     * Копирует файл в указанное место
     * 
     * @access public
     * @param string|object $dest
     * @return object
     */
    public function copy($dest)
    {
        
    }
    
    /**
     * Переименование/перемещение файла
     * 
     * @access public
     * @param string|object $dest
     * @return object
     */
    public function rename($dest)
    {
        
    }
    
    /**
     * Удаление файла
     * 
     * @access public
     * @return null
     */
    public function remove()
    {
        
    }
}

/**
 * Исключения генерируемый файлом
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
class FileException  extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
