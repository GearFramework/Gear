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
    const ALL = 0;
    const ONLY_FILES = 1;
    const ONLY_DIRS = 2;
    const SKIP_DOTS = 4;
    /* Private */
    /* Protected */
    protected $_current = null;
    protected $_filter = '*';
    protected $_flags = self::ALL;
    /* Public */
    
    /**
     * Установка фильтра
     * 
     * @access public
     * @param string|object|closure $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }
    
    /**
     * Возвращает фильтр
     * 
     * @access public
     * @return string|object|closure
     */
    public function getFilter() { return $this->_filter; }
    
    /**
     * Установка дополнительных флагов поиска
     * 
     * @access public
     * @param integer $flags
     * @return $this
     */
    public function setFlags($flags)
    {
        $this->_flags = $flags;
        return $this;
    }
    
    /**
     * Возвращает дополнительные флаги поиска
     * 
     * @access public
     * @return integer
     */
    public function getFlags() { return $this->_flags; }
    
    /**
     * Возвращает текущий элемент в директории
     * 
     * @access public
     * @return object
     */
    public function current() { return $this->_current; }
    
    /**
     * Возвращает полный путь текущего элемента
     * 
     * @access public
     * @return string
     */
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
     * @param string $filter
     * @param integer $flags
     * @return $this
     */
    public function open($filter = null, $flags = null) 
    { 
        if ($this->_handler)
            $this->close();
        $this->_handler = @opendir($this->path);
        if (!$this->_handler)
            $this->e('Cannot open file :fileName', ['fileName' => $this->path]);
        if ($filter !== null) $this->filter = $filter;
        if ($flags !== null) $this->flags = $flags;
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
        if (!$this->isReadable())
            $this->e('Directory :dirName is not readable', ['dirName' => $this->path]);
        if (($file = readdir($this->_handler)) !== false)
        {
            $valid = $this->event('onRead', $file);
            if (!$valid)
                $this->read();
            else
                $this->_current = $this->factory(['path' => $this->path . '/' . $file, 'filename' => $file]);
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
    
    /**
     * Выборка файлов по указанному шаблону и флагам
     *
     * @access public 
     * @param string $filter
     * @param integer $flags
     * @return $this
     */
    public function glob($filter = '*', $flags = self::ALL)
    {
        $this->filter = $filter;
        $this->flags = $flags;
        return $this;
    }
    
    /**
     * Возвращает true, если элемент пустой
     * 
     * @access public
     * @return boolean
     */
    public function isEmpty() 
    { 
        $this->open('*', self::SKIP_DOTS);
        return !$this->read();
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
    public function remove($removeIfNotEmpty = true)
    {
        if (!$removeIfNotEmpty && !$this->isEmpty())
            return $this;
        else
            return $this->_removeRecursive();
    }
    
    public function removeRecursive()
    {
        foreach($this->glob('*', self::SKIP_DOTS) as $item)
        {
            if ($item->isDir())
            {
                $result = $item->remove();
                if (!$result)
                    $this->e('Can not remove :fileName', ['fileName' => $item->path]);
            }
            else
            {
                try
                {
                    if (!$item->remove())
                        $this->e('Can not remove :fileName', ['fileName' => $item->path]);
                }
                catch(\Exception $e)
                {
                    return false;
                }
            }
        }
    }
    
    /**
     * Обработчик события всплывающего после функции readdir()
     * 
     * @access public
     * @param object $event
     * @param string $file
     * @return boolean
     */
    public function onRead($event, $file)
    {
        $valid = true;
        if ($this->_filter)
        {
            if (is_object($this->_filter))
                $valid = $this->_filter($file, $this->flags);
            else
            if (is_string($this->_filter))
            {
                $filter = '^' . str_replace(['.', '*'], ['\.', '(.*)?'], $this->_filter) . '$';
                $valid = preg_match('/' . $filter . '/', $file);
            }
        }
        if ($valid && $this->flags)
        {
            if (self::SKIP_DOTS&$this->flags)
            {
                if ($file === '.' || $file === '..')
                    $valid = false;
            }
            if (self::ONLY_FILES&$this->flags)
            {
                if (!is_file($this->path . '/' . $file))
                    $valid = false;
            }
            if (self::ONLY_DIRS&$this->flags)
            {
                if (!is_dir($this->path . '/' . $file))
                    $valid = false;
            }
        }
        return $valid;
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
