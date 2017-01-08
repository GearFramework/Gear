<?php

namespace gear\library\io\filesystem;

use gear\interfaces\IDirectory;

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
class GDirectory extends GFileSystem implements IDirectory
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

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
     * @return IDirectory
     * @since 0.0.1
     * @version 0.0.1
     */
    public function copy($destination): IDirectory
    {
        return $destination instanceof IDirectory ? $destination : GFileSystem::factory(['path' => $destination]);
    }

    /**
     * Создание директории
     * 
     * @return $this|IDirectory
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create(): IDirectory
    {
        if (!@mkdir($this)) {
            throw self::exceptionDirectoryNotCreated(['file' => $this]);
        }
        return $this;
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
}