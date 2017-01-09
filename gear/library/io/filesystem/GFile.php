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
class GFile extends GFileSystem implements IFile
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
     * @return IFile
     * @since 0.0.1
     * @version 0.0.1
     */
    public function copy($destination): IFile
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
     * @param null|int|string $mode
     * @return IFile
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create($mode = null): IFile
    {
        if (!touch($this)) {
            throw self::exceptionFileNotCreated(['file' => $this]);
        }
        if ($mode) {
            $this->chmod($mode);
        }
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
     * Возвращает true, если элемент файловой системы пустой, иначе false
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
     * Возвращает контент элемента файловой системы
     *
     * @param string $content
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setContent(string $content)
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
     *      '%01d %s',
     *      'kb'
     * )
     * @param string|array $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function size($format = '')
    {
        $size = filesize($this);
        if ($format) {
            $size = is_array($format) ? $this->formatSize($size, ... $format) : $this->formatSize($size, $format);
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
