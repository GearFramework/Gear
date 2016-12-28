<?php

namespace gear\library\io\filesystem;

use gear\Core;
use gear\interfaces\IFile;

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
     * Возвращает путь к файлу
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * Возращает timestamp доступа к файла
     *
     * @param string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function atime(string $format = '')
    {
        return $this->getAtime($format);
    }

    /**
     * Возвращает имя файла с расширением
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function basename(): string
    {
        return $this->getBasename();
    }

    /**
     * Возращает timestamp создания файла
     *
     * @param string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function ctime(string $format = '')
    {
        return $this->getCtime($format);
    }

    /**
     * Возвращает название папки, в которой лежит файл
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function dirname(): string
    {
        return $this->getDirname();
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
     * Возращает timestamp доступа к файла
     *
     * @param string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAtime(string $format = '')
    {
        return $format ? date($format, fileatime($this)) : fileatime($this);
    }

    /**
     * Возвращает имя файла с расширением
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getBasename(): string
    {
        return basename($this->path);
    }

    /**
     * Возращает timestamp создания файла
     *
     * @param string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCtime(string $format = '')
    {
        return $format ? date($format, filectime($this)) : filectime($this);
    }

    /**
     * Возвращает название папки, в которой лежит файл
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDirname(): string
    {
        return dirname($this->path);
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
     * Возращает timestamp модификации файла
     *
     * @param string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMtime(string $format = '')
    {
        return $format ? date($format, filemtime($this)) : filemtime($this);
    }

    /**
     * Возвращает имя файла без расширения
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getName(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * Возращает timestamp модификации файла
     *
     * @param string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function mtime(string $format = '')
    {
        return $this->getMtime($format);
    }

    /**
     * Возвращает имя файла без расширения
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function name(): string
    {
        return $this->getName();
    }

    /**
     * Установка пути файла
     *
     * @param string $path
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setPath(string $path)
    {
        $this->props('path', Core::resolvePath($path));
    }
}
