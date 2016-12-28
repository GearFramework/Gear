<?php

namespace gear\modules\resources\library;

use gear\interfaces\IDirectory;
use gear\interfaces\IFile;
use gear\library\GPlugin;
use gear\modules\resources\interfaces\IResourcePlugin;
use gear\traits\TView;

/**
 * Публикатор ресурсов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GResourcePlugin extends GPlugin implements IResourcePlugin
{
    /* Traits */
    use TView;
    /* Const */
    /* Private */
    /* Protected */
    protected $_allowedExtensions = [];
    protected $_mappingFolder = null;
    protected $_template = null;
    /* Public */

    /**
     * Возвращает массив расширений, которые может обрабатывать плагин
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAllowedExtensions(): array {
        return $this->_allowedExtensions;
    }

    /**
     * Возвращает название папки (доступной для веб-сервера), в которую отражаются файлы ресурсов
     *
     * @return string|IDirectory
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMappingFolder() {
        return $this->_mappingFolder;
    }

    /**
     * Возвращает название файла-шаблона
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getTemplate(): string
    {
        return $this->_template;
    }

    /**
     * Возвращает true, если указанный ресурс является js-файлом
     *
     * @param string|IFile $resource
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isAllowedResource($resource): bool
    {
        $resource = $this->prepareResource($resource);
        $ext = pathinfo($resource, PATHINFO_EXTENSION);
        return in_array(strtolower($ext), $this->allowedExtensions, true);
    }

    /**
     * Генерирует html для вставки на страницу
     *
     * @param string $url
     * @param array $options
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function makeHtml(string $url, array $options = []): string;

    /**
     * Маппит ресурс в доступную для веб-сервера папку и возвращает урл-ресурса
     *
     * @param $resource
     * @param bool $compile
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function mapping(IFile $resource, bool $compile = false): string
    {
        $mappingFolder = $_SERVER['DOCUMENT_ROOT'] .'/' . $this->mappingFolder;
        if (!is_writable($mappingFolder)) {
            throw self::exceptionFileSystem('Mapping directory <{folder}> is not writable', ['folder' => $mappingFolder]);
        }
        $mappingFile = md5($resource) . '.' . $resource->extension();
        $mappingResource = $mappingFolder . '/' . $mappingFile;
        if (!file_exists($mappingResource) || $resource->mtime() > filemtime($mappingResource)) {
            if ($compile) {
                $data = $this->view->renderFile($resource, [], true);
                file_put_contents($mappingResource, $data);
            } else {
                copy($resource, $mappingResource);
            }
        }
        return $this->mappingFolder . '/' . $mappingFile;
    }

    /**
     * Публикация ресурса, возвращает html-код для вставки на страницу
     *
     * @param string|IFile $resource
     * @param array $options
     * @param bool $compile
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function publicate($resource, array $options = [], bool $compile = false): string
    {
        $resource = $this->prepareResource($resource);
        if ($this->mappingFolder) {
            $url = $this->mapping($resource, $compile);
        } else {

        }
        return $this->makeHtml($url, $options);
    }

    /**
     * Установка папки (доступной для веб-сервера), в которую будут отражаться файлы ресурсов
     *
     * @param string|IDirectory $folder
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setMappingFolder($folder) {
        $this->_mappingFolder = $folder;
    }

    /**
     * Возвращает название файла-шаблона
     *
     * @param IDirectory|string $fileTemplate
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setTemplate($fileTemplate)
    {
        $this->_template = $fileTemplate;
    }
}