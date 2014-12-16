<?php

namespace gear\library;
use gear\Core;
use gear\library\GIo;
use gear\library\GException;
use gear\interfaces\IStaticFactory;

/**
 * Абстрактный класс элементов файловой системы
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
abstract class GFileSystem extends GIo implements IStaticFactory
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_factoryItem = 
    [
        'file' => ['class' => '\gear\library\GFile'],
        'dir' => ['class' => '\gear\library\GFolder'],
        'link' => ['class' => '\gear\library\GLink'],
    ];
    /* Public */
    
    /**
     * Возвращает путь к элементу файловой системы
     * 
     * @access public
     * @return string
     */
    public function __toString() { return $this->path; }
    
    /**
     * Создание экземляров элементов файловой системы
     * 
     * @access public
     * @static
     * @param string $name
     * @param array $args
     * @return object
     * @example GFileSystem::{'/var/www'}();
     * @example GFileSystem::{'/var/www/index.php'}();
     */
    public static function __callStatic($name, $args)
    {
        return self::factory(
        [
            'path' => $name, 
            'filename' => basename($name)
        ]);
    }
    
    /**
     * Фабркиа элементов файловой системы
     * 
     * @access public
     * @param array $properties
     * @throw FileSystemException
     * @return object
     */
    public static function factory(array $properties = [])
    {
        if (isset($properties['path']))
        {
            $type = filetype($properties['path']);
            $properties = array_merge(self::$_factoryItem[$type], $properties);
            list($class, $config, $properties) = Core::getRecords($properties);
            return new $class($properties);
        }
        static::e('Invalid item');
    }
    
    /**
     * Возвращает путь к элементу файловой системы
     * 
     * @access public
     * @return string
     */
    public function path() { return $this->path; }
    
    /**
     * Возвращает имя элемента файловой системы (имя+расширение)
     * 
     * @access public
     * @return string
     */
    public function getBasename() { return $this->basename(); }
    
    /**
     * Возвращает имя элемента файловой системы (имя+расширение)
     * 
     * @access public
     * @return string
     */
    public function basename() { return basename($this->path); }
    
    /**
     * Возвращает имя элемента файловой системы без расширения
     * 
     * @access public
     * @return string
     */
    public function getName() { return $this->name(); }
    
    /**
     * Возвращает имя элемента файловой системы без расширения
     * 
     * @access public
     * @return string
     */
    public function name() { return pathinfo($this->path, PATHINFO_FILENAME); }
    
    /**
     * Возвращает расширение элемента файловой системы
     * 
     * @access public
     * @return string
     * @see \gear\library\GFileSystem::ext();
     */
    public function getExtension() { return $this->extension(); }
    
    /**
     * Возвращает расширение элемента файловой системы
     * 
     * @access public
     * @return string
     * @see \gear\library\GFileSystem::ext();
     */
    public function ext() { return $this->extension(); }
    
    /**
     * Возвращает расширение элемента файловой системы
     * 
     * @access public
     * @return string
     */
    public function extension() { return pathinfo($this->path, PATHINFO_EXTENSION); }
    
    /**
     * Возвращает директорию в которой находится текущий элемент
     * 
     * @access public
     * @return object
     */
    public function getDirname() { return $this->dirname(); }
    
    /**
     * Возвращает директорию в которой находится текущий элемент
     * 
     * @access public
     * @return object
     */
    public function dirname() {  return dirname($this->path); }
    
    /**
     * Возвращает экземпляр класса GFodler
     * 
     * @access public
     * @return object
     */
    public function getDir() { return $this->dir(); }
    
    /**
     * Возвращает экземпляр класса GFodler
     * 
     * @access public
     * @return object
     */
    public function dir() { return $this->factory(['path' => $this->dirname()]); }
    
    /**
     * Возвращает true если элемент файловой системы существует, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function exists() { return file_exists($this->path); }
    
    /**
     * При $type равным
     * NULL - возращает тип элемента соответствующее одному из значений 
     *        GFileSystem::FILE|GFileSystem::FOLDER|GFileSystem::LINK
     * целочисленное значение 
     * целое число - возращает
     *        true или false при соответствии
     * строковое значение - возвращает true или false при
     *        соответствии
     * 
     * @access public
     * @param mixed $type
     * @return void
     */
    public function isa($type = null)
    {
        if ($type === null)
            return array_search($this->type(), $this->_types);
        else
        if (is_numeric($type))
            return array_search($this->type(), $this->_types) === (int)$type;
        else
        if (is_string($type))
            return $this->type() === $type;
        return false;
    }
    
    /**
     * Возвращает строковое значение соответсвующее типу элемента
     * 
     * @access public
     * @return string
     */
    public function type() { return filetype($this->path); }
    
    /**
     * Возвращает true если элемент является файлом, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isFile() { return is_file($this->path); }

    /**
     * Возвращает true если элемент является папкой, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isDir() { return is_dir($this->path); }

    /**
     * Возвращает true если элемент является ссылкой, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isLink() { return is_link($this->path); }
    
    /**
     * Возвращает true если элемент доступен для чтения
     * 
     * @access public
     * @return boolean
     */
    public function isReadable() { return is_readable($this->path); }
    
    /**
     * Возвращает true если элемент доступен для записи
     * 
     * @access public
     * @return boolean
     */
    public function isWritable() { return is_writable($this->path); }
    
    /**
     * Возвращает true если элемент доступен для запуска
     * 
     * @access public
     * @return boolean
     */
    public function isExecutable() { return is_executable($this->path); }
    
    /**
     * Возвращает размер элемента
     * 
     * @access public
     * @return integer
     */
    public function size($format = null, $force = '') 
    {
        $size = $this->getSize();
        return $this->_formatSize($format, $size, $force); 
    }
    
    /**
     * Форматирует значение размера элемента'
     * 
     * @access public
     * @param string $format
     * @param integer $size
     * @return void
     */
    protected function _formatSize($format, $bytes, $force)
    {
        $force = strtoupper($force);
        $defaultFormat = '%01d %s';
        if (!$format) 
            $format = '%01d %s';
        $bytes = max(0, (int) $bytes);
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $power = array_search($force, $units);
        if ($power === false) $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return sprintf($format, $bytes / pow(1024, $power), $units[$power]);
    
        $filesizename = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $i = floor(log($size, 1024));
        return $size ? round($size/pow(1024, $i), 2) . $filesizename[$i] : '0 ' . $filesizename[0];            
    }
    
    /**
     * Смена прав доступа к элементу
     * 
     * @access public
     * @param integer|string $permission
     * @return $this
     */
    public function chmod($permission)
    {
        if (is_integer($permission))
        { 
            if (!@chmod($this->path, $permission))
                $this->e('Permission denied :fileName', ['fileName' => $this->path]);
        }
        else
        if (is_string($permission))
        {
            if ($permission[0] === 'u' || $permission[0] === 'g' || $permission[0] === 'o')
            {

            }
        }
        else
            $this->e('Invalid value of permission :permission'. ['permission' => $permission]);
        return $this;
    }

    public function atime($format = null) { return !$format ? fileatime($this) : $this->_formatTime(fileatime($this), $format); }

    public function ctime($format = null) { return !$format ? filectime($this) : $this->_formatTime(filectime($this), $format); }

    public function mtime($format = null) { return !$format ? filemtime($this) : $this->_formatTime(filemtime($this), $format); }

    private function _formatTime($time, $format)
    {

    }
    
    /**
     * Возвращает true, если элемент пустой
     * 
     * @abstract
     * @access public
     * @return void
     */
    abstract public function isEmpty();

    /**
     * Возвращает размер элемента в байтах
     * 
     * @abstract
     * @access public
     * @return integer
     */
    abstract public function getSize();
    
    /**
     * Создание элемента файловой системы
     * 
     * @abstract
     * @access public
     * @param null|integer|string $permission
     * @param boolean $overwriteIfExists
     * @return $this
     */
    abstract public function create($permission = null, $overwriteIfExists = true);
    
    /**
     * Копирует текущий элемент в указанное место
     * 
     * @abstract
     * @access public
     * @param string|object $dest
     * @return object
     */
    abstract public function copy($dest);
    
    /**
     * Переименовывает/перемещает текущий элемент в указанное место
     * 
     * @abstract
     * @access public
     * @param string|object $dest
     * @return object
     */
    abstract public function rename($dest);
    
    /**
     * Удаляет элемент из файловой системы
     * 
     * @abstract
     * @access public
     * @return null
     */
    abstract public function remove();
}

/**
 * Исключения элементов файловой системы
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
class FileSystemException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
