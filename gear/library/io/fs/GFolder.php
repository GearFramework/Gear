<?php

namespace gear\library\io\fs;
use gear\Core;
use gear\library\io\fs\GFileSystem;
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
        if (!$this->_handler)
            $this->open();
        else
            rewinddir($this->_handler);
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
        if (!($this->_handler = @opendir($this->path)))
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
    
    /**
     * Если $data является файлом или каталогом, то копирует его в данную 
     * директорию, иначе в директории создаётся временный файл, в который
     * записыватся содержимое $data
     * 
     * @access public
     * @param mixed $data
     * @return $this
     */
    public function write($data = null) 
    {
        if (is_object($data) && $data instanceof \gear\library\GFileSystem)
            $data->copy($this->path);
        else
        if (is_string($data) && file_exists($data))
            GFileSystem::factory(['path' => $data])->copy($this->path);
        else
            GFileSystem::factory(['path' => tempnam($this->path)])->write($data);
        return $this;
    }
    
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
        $size = 0;
        foreach($this->glob('*', SELF::SKIP_DOTS) as $item)
            $size += $item->isDir() ? $item->getSize() : $item->getSize();
        return $size;
    }
    
    /**
     * Создание элемента файловой системы
     * 
     * @access public
     * @param null|integer|string $permission
     * @param boolean $overwriteIfExists
     * @return $this
     */
    public function create($permission = null, $overwriteIfExists = true)
    {
        if ($overwriteIfExists && $this->exists())
            $this->e('Folder :folderName already exists', ['folderName' => $this->path]);
        if (!$this->dir()->isWritable() || !@mkdir($this->path))
            $this->e('Can not create folder :folderName', ['folderName' => $this->path]);
        return $permission ? $this->chmod($permission) : $this;
    }

    /**
     * Копирует папку в указанное место
     * 
     * @access public
     * @param string|object $dest
     * @param null|integer|string $permission
     * @return object
     */
    public function copy($dest, $permission = null)
    {
        if (file_exists($dest) && !is_writable($dest))
            $this->e('Can not copy directory :folderName to :dest', ['folderName' => $this->path, 'dest' => $dest]);
        if (!file_exists($dest))
            $this->e('Destination directory :folderName not exists', ['folderName' => $dest]);
        $dest = $dest . '/' . $this->basename();
        mkdir($dest);
        $dest = self::factory(['path' => $dest]);
        $this->_copyRecursive($dest);
        if ($permission !== null)
            $dest->chmod($permission);
        return $dest;
    }

    /**
     * Рекурсивное удаление директории
     * 
     * @access protected
     * @param string|object $dest
     * @return boolean
     */
    protected function _copyRecursive($dest)
    {
        foreach($this->glob('*', self::SKIP_DOTS) as $item)
        {
            if ($item->copy($dest) === false)
                $this->e('Can not copy :fileName', ['fileName' => $this->path]);
        }
        return true;
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
        if (!$this->isWritable() || !isWritable($dest))
            $this->e('Can not rename directory :folderName to :dest', ['folderName' => $this->path, 'dest' => $dest]);
        @rename($this->path(), $dest);
        return is_object($dest) ? $dest : GFileSystem::factory(['path' => $dest]);
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
            $this->e('Directory :folderName not empty', ['folderName' => $this->path]);
        else
        if (!$this->isWritable())
            $this->e('Can not remove directory :folderName', ['folderName' => $this->path]);
        else
        {
            $this->_removeRecursive();
            return @rmdir($this->path) ? true : false;
        }
    }
    
    /**
     * Рекурсивное удаление директории
     * 
     * @access protected
     * @return boolean
     */
    protected function _removeRecursive()
    {
        foreach($this->glob('*', self::SKIP_DOTS) as $item)
        {
            if ($item->remove(true) === false)
                $this->e('Can not remove :fileName', ['fileName' => $this->path]);
        }
        return true;
    }
    
    /**
     * Смена директории на указанную, относительно текущей
     * 
     * @access public
     * @param null|string $dir
     * @return object
     */
    public function chDir($dir = null) 
    {
        $dir = !$dir ? $this : self::factory(['path' => $this . '/' . $dir]);
        chdir($dir->path);
        return $dir; 
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
