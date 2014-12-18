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
        if (get_called_class() !== __CLASS__)
            return static::factory(
            [
                'class' => get_called_class(),
                'path' => $name, 
                'filename' => basename($name)
            ]);
        else
            return static::factory(
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
            if (!isset($properties['class']))
            {
                $type = filetype($properties['path']);
                $properties = array_merge(self::$_factoryItem[$type], $properties);
            }
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
        if (is_numeric($permission))
            $res = @chmod($this->path, $permission);
        else
        if (is_string($permission))
        {
            $permission = str_replace(' ', '', $permission);
            if (strpos($permission, ','))
                $permission = $this->_chmodRelative(explode(',', $permission));
            else
            if ($permission[0] === 'u' || $permission[0] === 'g' || $permission[0] === 'o' || $permission[0] === 'a')
                $permission = $this->_chmodRelative([$permission]);
            else
                $permission = $this->_chmodTarget($permission);
            $res = @chmod($this->path, $permission);
        }
        else
            $this->e('Invalid value of permission :permission'. ['permission' => $permission]);
        if (!$res)
            $this->e('can not change mode :fileName', ['fileName' => $this->path]);
        return $this;
    }
    
    /**
     * Возвращает права доступа
     * Если $asString установлен в true, то метод вернёт строковое 
     * представление в виде: rwxrwxrwx
     * 
     * @access public
     * @param bool $asString
     * @return numeric|string
     */
    public function getMode($asString = false)
    {
        $mode = substr(decoct(fileperms($this->path)), -4);
        if ($asString)
        {
            $mode = fileperms($this->path);
            $a = [0xC000 => 's', 0xA000 => 'l', 0x8000 => '-', 0x6000 => 'b', 0x4000 => 'd', 0x2000 => 'c', 0x1000 => 'p'];
            $p = null;
            foreach($a as $d => $type)
                if (($mode & $d) == $d) { $perm = $type; break; }
            if (!$perm) $perm = 'u';
            $perm .= (($mode & 0x0100) ? 'r' : '-');
            $perm .= (($mode & 0x0080) ? 'w' : '-');
            $perm .= (($mode & 0x0040) ? (($mode & 0x0800) ? 's' : 'x' ) : (($mode & 0x0800) ? 'S' : '-'));
            $perm .= (($mode & 0x0020) ? 'r' : '-');
            $perm .= (($mode & 0x0010) ? 'w' : '-');
            $perm .= (($mode & 0x0008) ? (($mode & 0x0400) ? 's' : 'x' ) : (($mode & 0x0400) ? 'S' : '-'));
            $perm .= (($mode & 0x0004) ? 'r' : '-');
            $perm .= (($mode & 0x0002) ? 'w' : '-');
            $perm .= (($mode & 0x0001) ? (($mode & 0x0200) ? 't' : 'x' ) : (($mode & 0x0200) ? 'T' : '-'));
            $mode = $perm;
        }
        return $mode;
    }
    
    /**
     * Преобразует значения вида g+rw,uo-x в восмеричное представление
     * 
     * @access private
     * @param array $permission
     * @return numeric
     */
    private function _chmodRelative($permission)
    {
        $mode = (string)$this->getMode();
        $str2Oct = function($strMode)
        {
            $d = 0;
            $l = strlen($strMode);
            for($i = 0; $i < $l; ++ $i)
            {
                if ($strMode[$i] == 'r') $d = $d | 4;
                else
                if ($strMode[$i] == 'w') $d = $d | 2;
                else
                if ($strMode[$i] == 'x' || $strMode[$i] == 's' || $strMode[$i] == 't') $d = $d | 1;
            }
            return $d;
        };
        $set = function($type, $mode, $value, $op)
        {
            foreach($type as $t)
                $op == '+' ? $mode[$t] | $value : ($op == '-' ? $mode[$t] ^ $value : $mode[$t] = $value);
            return $mode;
        };
        foreach($permission as $modes)
        {
            $p = preg_split('/([+-=])/', $modes, -1, PREG_SPLIT_DELIM_CAPTURE);
            $ugo = $p[0]; $op = $p[1]; $o = ['u' => 1, 'g' => 2, 'o' => 3, 'a' => [1, 2, 3]];
            for($i = 0; $i < strlen($ugo); ++ $i)
            {
                $d = $str2Oct($p[2]);
                $mode = $set($o[$ugo[$i]], $mode, $d, $op);
            }
        }
        return $mode;
    }
    
    /**
     * Преобразует значения вида rwxrwxrwx в восмеричное представление
     * 
     * @access private
     * @param string $permission
     * @return numeric
     */
    private function _chmodTarget($permission)
    {
        $res = '0';
        $perms = explode("\n", chunk_split($permission, 3, "\n"));
        $getValue = function($perm, $pos, &$res)
        {
            $value = 0;
            if (isset($perm[0]) && $perm[0] == 'r')
                $value = $value | 4;
            if (isset($perm[1]) && $perm[1] == 'w')
                $value = $value | 2;
            if (isset($perm[2]))
            {
                if ($perm[2] === 'x' || $perm[2] === 's' || $perm[2] === 't')
                    $value = $value | 1;
                if ($perm[2] === 's')
                {
                    if ($pos === 0)
                        $res[0] = 4;
                    else
                    if ($pos === 1)
                        $res[0] = 2;
                }
                else
                if ($perm[2] === 't' && $pos === 2)
                    $res[0] = 1;
           }
            return $value;
        };
        for($i = 0; $i < 3; ++ $i)
            isset($perms[$i]) ? $res .= $getValue($perms[$i], $i, $res) : $res .= '0';
        return $res;
    }

    /**
     * Возвращает время последнего доступа к файлу
     * 
     * @access public
     * @param null|string $format
     * @return timestamp|string
     */
    public function atime($format = null) { return !$format ? fileatime($this) : $this->_formatTime(fileatime($this), $format); }

    /**
     * Возвращает время изменения индексного дескриптора файла
     * 
     * @access public
     * @param null|string $format
     * @return timestamp|string
     */
    public function ctime($format = null) { return !$format ? filectime($this) : $this->_formatTime(filectime($this), $format); }

    /**
     * Возвращает время последнего изменения файла
     * 
     * @access public
     * @param null|string $format
     * @return timestamp|string
     */
    public function mtime($format = null) { return !$format ? filemtime($this) : $this->_formatTime(filemtime($this), $format); }

    /**
     * Форматирование даты
     * 
     * @access private
     * @param timestamp $time
     * @param string $format
     * @return string
     */
    private function _formatTime($time, $format) { return (new \gear\helpers\GCalendar())->getDate($time)->format($format); }
    
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
