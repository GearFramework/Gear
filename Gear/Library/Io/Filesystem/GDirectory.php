<?php

namespace Gear\Library\Io\Filesystem;

use Gear\Core;
use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileSystemInterface;
use Traversable;

/**
 * Класс директорий
 *
 * @package Gear Framework
 *
 * @property mixed content
 * @property string path
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GDirectory extends GFileSystem implements DirectoryInterface, \IteratorAggregate
{
    /* Traits */
    /* Const */
    const DEFAULT_MODE = 0775;
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Меняет владельца (пользователя/группу) элемента файловой системы
     *
     * @param int|string|array|GFileSystemOptions $options
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function chown($options = [])
    {
        if (is_string($options) ||is_numeric($options)) {
            if (($pos = strpos($options, ':')) !== false) {
                $own = explode(':', $options);
                $options = $pos === 0 ? ['group' => $own[0]] : ['user' => $own[0], 'group' => $own[1]];
            } else {
                $options = ['user' => $options];
            }
        }
        $options = $this->_prepareOptions($options);
        parent::chown($options);
        if ($options->recursive) {
            foreach ($this as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $item = $this->factory(['path' => $this . '/' . $item]);
                $item->chown($options);
            }
        }
    }

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
            closedir($this->_handler);
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
     * @param string|DirectoryInterface $destination
     * @param array|GFileSystemOptions $options
     * @return DirectoryInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function copy($destination, $options = []): FileSystemInterface
    {
        $options = $this->_prepareOptions($options);
        if (is_string($destination)) {
            $destination = $this->factory(['path' => Core::resolvePath($destination)]);
        }
        /** @var FileSystemInterface $target */
        $target = $this->factory(['path' => $destination . '/' . $this->basename()]);
        $this->beforeCopy($destination, $target, $options);
        if (!$target->exists()) {
            $target->create();
        }
        foreach (scandir($this) as $item) {
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
     * @return FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function create($options = []): FileSystemInterface
    {
        $options = $this->_prepareOptions($options);
        if (!mkdir($this, $options->permission ?: self::DEFAULT_MODE, $options->recursive ?: false)) {
            throw self::FileSystemException('Failed to create directory <{dir}>', ['dir' => $this]);
        }
        return $this;
    }

    /**
     * Возвращает контент файла
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getContent(): array
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
            while (($item = $this->read()) !== false) {
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
     * @return DirectoryInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function open($options = []): FileSystemInterface
    {
        if (!$this->isOpened()) {
            $options = $this->_prepareOptions($options);
            $this->_handler = opendir($this);
        }
        return $this;
    }

    /**
     * Чтение дочерних элементов из директории
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
        return readdir($this->_handler);
    }

    /**
     * Удаление директории
     *
     * @param array $options
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove($options = [])
    {
        $options = $this->_prepareOptions($options);
        if ($this->beforeRemove($options)) {
            foreach ($this as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $item = $this->factory(['path' => $this . '/' . $item]);
                $item->remove();
            }
            if (!rmdir($this)) {
                throw self::FileSystemException('Failed to delete directory <{dir}>', ['dir' => $this]);
            }
        }
    }

    /**
     * Переименование/перемещение дирeктории
     *
     * @param string|FileSystemInterface $destination
     * @param array|GFileSystemOptions $options
     * @return DirectoryInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function rename($destination, $options = []): FileSystemInterface
    {
        $options = $this->_prepareOptions($options);
        if (is_string($destination)) {
            $destination = $this->factory(['path' => Core::resolvePath($destination)]);
        }
        $this->beforeRename($destination, $options);
        if (!rename($this, $destination)) {
            throw static::exceptionFileSystem('Failed rename from <{source}> to <{destination}>', ['source' => $this, 'destination' => $destination]);
        }
        return $destination;
    }

    public function seek($offset) {}

    /**
     * Возвращает контент элемента файловой системы
     *
     * @param mixed $content
     * @return bool|FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
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
        foreach ($this as $item) {
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

    public function write($data, $length = 0) {}
}
