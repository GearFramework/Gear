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
            $this->e('Can not open file :fileName', ['fileName' => $this->path]);
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
     * Возвращает true, если элемент пустой
     * 
     * @access public
     * @return boolean
     */
    public function isEmpty() { return !$this->getSize(); }
    
    /**
     * Возвращает размер элемента в байтах
     * 
     * @access public
     * @return integer
     */
    public function getSize() { return filesize($this->path()); }
    
    /**
     * Копирует файл в указанное место
     * 
     * @access public
     * @param string|object $dest
     * @return object
     */
    public function copy($dest, $permission = null)
    {
        if ((file_exists($dest) && !is_writable($dest)) || !is_writable(dirname($dest)))
            $this->e('Can not copy file :fileName to :destName', ['fileName' => $this->path, 'destName' => $dest]);
        copy($this, $dest);
        if ($permission !== null)
            @chmod($dest, $permission);
        return GFileSystem::factory(['path' => $dest]);
    }
    
    /**
     * Переименование/перемещение файла
     * 
     * @access public
     * @param string|object $dest
     * @return object
     */
    public function rename($dest, $permission = null)
    {
        if ((file_exists($dest) && !is_writable($dest)) || !is_writable(dirname($dest)))
            $this->e('Can not rename file :fileName to :destName', ['fileName' => $this->path, 'destName' => $dest]);
        rename($this, $dest);
        if ($permission !== null)
            @chmod($dest, $permission);
        return GFileSystem::factory(['path' => $dest]);
    }
    
    /**
     * Удаление файла
     * 
     * @access public
     * @return null
     */
    public function remove()
    {
        if (!$this->isWritable())
            $this->e('Can not remove file :fileName', ['fileName' => $this->path]);
        return @unlink($this->path);
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
