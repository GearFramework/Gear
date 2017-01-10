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
     * @param array $options
     * @return IDirectory
     * @since 0.0.1
     * @version 0.0.1
     */
    public function copy($destination, array $options = []): IDirectory
    {
        if (is_string($destination)) {
            $destination = GFileSystem::factory(['path' => $destination]);
        }
        if (!$destination->exists()) {
            $destination->create();
        }
        $result = GFileSystem::factory(['path' => $destination . '/' . $this]);
        if (!$result->exists()) {
            $result->create($options);
        } else if (!isset($options['overwrite']) || !$options['overwrite']) {
            throw self::exceptionFileCopyError('Destination directory <{dest}> alreadey exists', ['dest' => $result]);
        }
        foreach(scandir($this) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item = GFileSystem::factory(['path' => $this . '/' . $item]);
            $item->copy($result);
        }
        return $result;
    }

    /**
     * Создание директории
     *
     * @param array $options
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create(array $options = [])
    {
        if ($this->exists()) {
            if (isset($options['overwrite']) && $options['overwrite']) {
                $this->remove();
            }
        }
        if (!@mkdir($this)) {
            throw self::exceptionDirectoryNotCreated('Directory <{dir}> already exists', ['dir' => $this]);
        }
        if (isset($options['mode'])) {
            $this->chmod($options['mode']);
        }
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
     * Удаление директории
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove()
    {
        foreach($this as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item = GFileSystem::factory(['path' => $this . '/' . $item]);
            $item->remove();
        }
        if (!@rmdir($this)) {
            throw self::exceptionFileRemove('Failed to delete file <{file}>', ['file' => $this]);
        }
    }
}