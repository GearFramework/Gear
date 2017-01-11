<?php

namespace gear\library\io\filesystem;

use gear\Core;
use gear\interfaces\IFile;
use gear\interfaces\IFileSystem;

/**
 * Класс файлов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GFile extends GFileSystem implements IFile, \IteratorAggregate
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

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
     * @param string|IFile $destination
     * @param array|GFileSystemOptions $options
     * @return IFile
     * @since 0.0.1
     * @version 0.0.1
     */
    public function copy($destination, $options = []): IFile
    {
        $result = copy($this, Core::resolvePath($destination));
        if (!$result) {
            throw static::exceptionErrorFileCopy(['source' => $this, 'destination' => $destination]);
        }
        return $destination instanceof IFile ? $destination : GFileSystem::factory(['path' => $destination]);
    }

    /**
     * Создание файла
     *
     * @param array|GFileSystemOptions $options
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create($options = [])
    {
        if ($this->exists()) {
            if (isset($options['overwrite']) && $options['overwrite']) {
                $this->remove();
            }
        }
        if (!touch($this)) {
            throw self::exceptionFileNotCreated('File <{file}> already exists', ['file' => $this]);
        }
        if (isset($options['mode'])) {
            $this->chmod($options['mode']);
        }
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
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function file(): array
    {
        return file($this, FILE_IGNORE_NEW_LINES);
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
        $data = @file_get_contents($this);
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
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->file());
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
            throw self::exceptionFileSystem('Failed to delete file <{file}>', ['file' => $this]);
        }
    }

    /**
     * Возвращает контент элемента файловой системы
     *
     * @param string $content
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setContent($content)
    {
        if (!@file_put_contents($this, $content)) {
            throw static::exceptionErrorSetContent(['file' => $this]);
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
}
