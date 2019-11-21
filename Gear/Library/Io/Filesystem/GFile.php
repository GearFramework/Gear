<?php

namespace Gear\Library\Io\Filesystem;

use Gear\Core;
use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileInterface;
use Gear\Interfaces\FileSystemInterface;

/**
 * Класс файлов
 *
 * @package Gear Framework
 *
 * @property mixed content
 * @property string ext
 * @property string extension
 * @property string mime
 * @property string path
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GFile extends GFileSystem implements FileInterface, \IteratorAggregate
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Закрывает файл
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function close()
    {
        if ($this->isOpened()) {
            fclose($this->_handler);
        }
    }

    /**
     * Возвращает контент элемента файловой системы
     *
     * @param null|string|\Closure $prepareHandler
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function content($prepareHandler = null): string
    {
        $data = $this->getContent();
        return $prepareHandler && is_callable($prepareHandler) ? $prepareHandler($data) : $data;
    }

    /**
     * Копирование элемента файловой системы
     *
     * @param string|DirectoryInterface $destination
     * @param array|GFileSystemOptions $options
     * @return FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function copy($destination, $options = []): FileSystemInterface
    {
        /** @var GFileSystemOptions $options */
        $options = $this->_prepareOptions($options);
        if (is_dir($destination)) {
            $target = $destination . '/' .  $this->basename();
        } else {
            $target = (string)$destination;
        }
//        $this->beforeCopy($destination, $target, $options);
        $result = copy($this, $target);
        if (!$result) {
            throw self::FileSystemException('Failed to copy from <{source}> to <{destination}>', ['source' => $this, 'destination' => $target]);
        }
        /** @var FileSystemInterface $target */
        $target = $this->factory(['path' => $target]);
        return $target;
    }

    /**
     * Создание файла
     *
     * @param array|GFileSystemOptions $options
     * @return FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function create($options = []): FileSystemInterface
    {
        $options = $this->_prepareOptions($options);
        $this->beforeCreate($options);
        if (touch($this) === false) {
            throw self::FileSystemException('Failed to create file <{file}>', ['file' => $this]);
        }
        $this->afterCreate($options);
        return $this;
    }

    /**
     * Возвращает расширение файла
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function ext(): string
    {
        return $this->getExt();
    }

    /**
     * Возвращает расширение файла
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function extension(): string
    {
        return $this->getExtension();
    }

    /**
     * Возвращает массив строк из файла
     * @param array $options
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function file($options = []): array
    {
        $options = $this->_prepareOptions($options);
        return file($this, $options->ignoreNewLines ? FILE_IGNORE_NEW_LINES : 0);
    }

    /**
     * Возвращает контент файла
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getContent(): string
    {
        $data = file_get_contents($this);
        if ($data === false) {
            $data = '';
        }
        return $data;
    }

    /**
     * Возвращает расширение файла
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getExt(): string
    {
        return $this->getExtension();
    }

    /**
     * Возвращает расширение файла
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->file(['ignoreNewLines' => true]));
    }

    /**
     * Возвращает mime-тип файла
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMime(): string 
    {
        return self::$_mimes[$this->ext()] ?? 'text/plain';
    }

    /**
     * Возвращает true, если файл пустой, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isEmpty(): bool
    {
        return (bool)filesize($this);
    }

    /**
     * Открывает файл чтения/записи
     *
     * @param array $options
     * @return FileInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function open($options = []): FileInterface
    {
        if (!$this->isOpened()) {
            $options = $this->_prepareOptions($options);
            $this->_handler = fopen($this, $options->mode);
        }
        return $this;
    }

    /**
     * Читение из файла
     *
     * @param int $length
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function read($length = 0)
    {
        if (!$this->isOpened()) {
            $this->open();
        }
        return fread($this->_handler, $length);
    }

    public function readArray(int $flags = FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
    {
        return file($this->path, $flags);
    }

    /**
     * Удаление файла
     *
     * @param array $options
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($options = [])
    {
        $options = $this->_prepareOptions($options);
        if (!@unlink($this)) {
            throw self::FileSystemException('Failed to delete file <{file}>', ['file' => $this]);
        }
    }

    /**
     * Переименование/перемещение файла
     *
     * @param string|FileSystemInterface $destination
     * @param array|GFileSystemOptions $options
     * @return FileInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function rename($destination, $options = []): FileInterface
    {
        /**
         * @var FileInterface $destination
         */
        $options = $this->_prepareOptions($options);
        $this->beforeRename($destination, $options);
        if (!rename($this, $destination)) {
            throw static::FileSystemException('Failed rename from <{source}> to <{destination}>', ['source' => $this, 'destination' => $destination]);
        }
        $this->path = $destination;
        return $this;
    }

    public function seek($offset)
    {
        if (!$this->isOpened()) {
            $this->open();
        }
        fseek($this->_handler, $offset);
    }

    /**
     * Возвращает контент элемента файловой системы
     *
     * @param string $content
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setContent($content)
    {
        if (!file_put_contents($this, $content)) {
            throw static::FileSystemException('Failed set content to file <{file}>', ['file' => $this]);
        }
    }

    /**
     * Возвращает размер элемента файловой системы
     * $format может принимать строку в котором возвратить размер файла
     *      '%01d %s'
     * или массив
     * Array (
     *      'format' => '%01d %s',
     *      'force' => 'kb'
     * )
     * @param array|GFileSystemOptions $options
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function size($options = ['format' => ''])
    {
        $options = $this->_prepareOptions($options);
        $size = filesize($this);
        if ($options->format) {
            $size = $this->formatSize($size, $options->format, $options->force);
        }
        return $size;
    }

    /**
     * Возвращает строковое значение соответствующее типу элемента
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function type()
    {
        return filetype($this->path);
    }

    public function write($data, $length = 0)
    {
        if (!$this->isOpened()) {
            $this->open();
        }
        return fwrite($this->_handler, $data, $length);
    }
}
