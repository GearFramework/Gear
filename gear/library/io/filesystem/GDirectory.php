<?php

namespace gear\library\io\filesystem;

use gear\interfaces\IDirectory;
use gear\interfaces\IFileSystem;
use Traversable;

/**
 * Класс директорий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GDirectory extends GFileSystem implements IDirectory, \IteratorAggregate
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Закрывает директорию
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function close()
    {
        if ($this->isOpened()) {
            @closedir($this->_handler);
        }
    }

    /**
     * Возвращает списко файлов в директории
     *
     * @param null|string|\Closure $prepareHandler
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function content($prepareHandler = null): array
    {
        $data = $this->getContent();
        return $prepareHandler && is_callable($prepareHandler) ? $prepareHandler($data) : $data;
    }

    /**
     * Копирование элемента файловой системы
     *
     * @param string|IDirectory $destination
     * @param array|GFileSystemOptions $options
     * @return IDirectory
     * @since 0.0.1
     * @version 0.0.1
     */
    public function copy($destination, $options = []): IDirectory
    {
        $options = $this->_prepareOptions($options);
        if (is_string($destination)) {
            $destination = $this->factory(['path' => Core::resolvePath($destination)]);
        }
        $target = $this->factory(['path' => $destination . '/' . $this->basename()]);
        $this->beforeCopy($destination, $target, $options);
        if (!$target->exists()) {
            $target->create();
        }
        foreach(scandir($this) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item = GFileSystem::factory(['path' => $this . '/' . $item]);
            $item->copy($target);
        }
        return $target;
    }

    /**
     * Создание директории
     *
     * @param array|GFileSystemOptions $options
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create($options = [])
    {
        $options = $this->_prepareOptions($options);
        $this->beforeCreate($options);
        if (!@mkdir($this)) {
            throw self::exceptionFileSystem('Failed to create directory <{dir}>', ['dir' => $this]);
        }
        $this->afterCreate($options);
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
        return scandir($this);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->content());
    }

    /**
     * Возвращает true, если директория пустая, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isEmpty(): bool
    {
        $isEmpty = true;
        if ($this->exists()) {
            while(($item = $this->read()) !== false) {
                if ($item !== '.' && $item !== '..') {
                    $isEmpty = false;
                    break;
                }
            }
            $this->close();
        }
        return $isEmpty;
    }

    /**
     * Открывает директорию для чтения
     *
     * @param array $options
     * @return IDirectory
     * @since 0.0.1
     * @version 0.0.1
     */
    public function open($options = []): IDirectory
    {
        if (!$this->isOpened()) {
            $options = $this->_prepareOptions($options);
            $this->_handler = opendir($this);
        }
        return $this;
    }

    /**
     * Читайте дочерние элементы из директории
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function read()
    {
        if (!$this->isOpened()) {
            $this->open();
        }
        return readdir($this->_handler);
    }

    /**
     * Удаление директории
     *
     * @param array $options
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($options = [])
    {
        $options = $this->_prepareOptions($options);
        $this->beforeRemove($options);
        foreach($this as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item = $this->factory(['path' => $this . '/' . $item]);
            $item->remove();
        }
        if (!@rmdir($this)) {
            throw self::exceptionFileSystem('Failed to delete directory <{dir}>', ['dir' => $this]);
        }
    }

    /**
     * Возвращает контент элемента файловой системы
     *
     * @param mixed $content
     * @return bool|IFileSystem
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setContent($content)
    {
        if (is_string($content) && file_exists($content)) {
            $content = $this->factory(['path' => $content]);
        }
        if ($content instanceof IFileSystem) {
            $content->copy($this);
        } else {
            $content = false;
        }
        return $content;
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
        $size = 0;
        foreach($this as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item = $this->factory(['path' => $this . '/' . $item]);
            $size += $item->size();
            if ($options->format) {
                $size = $this->formatSize($size, $options->format, $options->force);
            }
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
}
