<?php

namespace gear\library;
use gear\Core;
use gear\library\GIo;
use gear\library\GException;
use gear\interfaces\IFactory;

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
abstract class GFileSystem extends GIo implements IFactory
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_factoryItem = 
    [
        'file' => ['class' => '\gear\library\GFile'],
        'folder' => ['class' => '\gear\library\GFolder'],
        'link' => ['class' => '\gear\library\GLink'],
    ]
    /* Public */
    
    /**
     * Возвращает путь к элементу файловой системы
     * 
     * @access public
     * @return string
     */
    public function __toString() { return $this->path; }
    
    /**
     * Фабркиа элементов файловой системы
     * 
     * @access public
     * @param array $properties
     * @throw FileSystemException
     * @return object
     */
    public function factory(array $properties = [])
    {
        if (isset($properties['path']))
        {
            $type = $this->type();
            $properties = array_merge($this->_factoryItem[$type], $properties);
            list($class, $config, $properties) = Core::getRecords($properties);
            return new $class($properties);
        }
        $this->e('Invalid item');
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
    public function basename() { return basename($this->path); }
    
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
    public function ext() { return $this->extension(); }
    
    /**
     * Возвращает расширение элемента файловой системы
     * 
     * @access public
     * @return string
     */
    public function extension() { return pathinfo($this->path, PATHINFO_EXTENSION); }
    
    /**
     * Возвращает объект GFolder в котором находится текущий элемент
     * 
     * @access public
     * @return object
     */
    public function dirname() {  return dirname($this->path); }
    
    /**
     * Возвращает экземпляр класса GFodler
     * 
     * @return
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
     * Возвращает размер элемента
     * 
     * @abstract
     * @access public
     * @return integer
     */
    abstract public function size();

    /**
     * Копирует текущий элемент в указанное место
     * 
     * @abstract
     * @access public
     * @param string|object $dest
     * @return integer
     */
    abstract public function copy($dest);
    
    /**
     * Переименовывает/перемещает текущий элемент в указанное место
     * 
     * @abstract
     * @access public
     * @param string|object $dest
     * @return integer
     */
    abstract public function rename($dest);
    
    /**
     * Удаляет элемент из файловой системы
     * 
     * @abstract
     * @access public
     * @return void
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
