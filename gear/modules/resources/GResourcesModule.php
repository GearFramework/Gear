<?php

namespace gear\modules\resources;

use gear\Core;
use gear\interfaces\IFile;
use gear\library\GModule;
use gear\library\io\filesystem\GFile;

/**
 * Менеджер публикации пользовательских ресурсов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GResourcesModule extends GModule
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'plugins' => [
            'js' => [
                'class' => '\gear\modules\resources\plugins\GJsResourcesPlugin',
                'allowedExtensions' => ['js'],
            ],
            'css' => [
                'class' => '\gear\modules\resources\plugins\GCssResourcesPlugin',
                'allowedExtensions' => ['css'],
            ],
        ],
    ];
    protected static $_initialized = false;
    protected $_resources = ['js', 'css'];
    protected $_cacheName = 'cache';
    /* Public */

    /**
     * Возвращает ресурс или контент ресурса по указанному хэшу
     *
     * @param string $hash
     * @param string $type
     * @param bool $send
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function get(string $hash, string $type, bool $send = false)
    {
        $resource = '';
        foreach($this->resources as $resourceName) {
            if ($this->p($resourceName)->isAllowedTypeResource($type)) {
                $resource = $this->p($resourceName)->get($hash);
                if ($send) {
                    $this->p($resourceName)->send($resource);
                }
                break;
            }
        }
        return $resource;
    }

    /**
     * Возвращает инстанс плагина для работы с кэшем или null, таковой не зарегистрирован
     *
     * @return null|ICache
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCache()
    {
        $cache = null;
        if ($this->isPluginRegistered($this->_cache)) {
            $cache = $this->p($this->_cache);
        }
        return $cache;
    }

    /**
     * Возвращает список плагинов-публикаторов ресурсов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getResources(): array
    {
        return $this->_resources;
    }

    /**
     * Подготовка ресурса
     *
     * @param string|IFile $resource
     * @return IFile
     * @since 0.0.1
     * @version 0.0.1
     */
    public function prepareResource($resource): IFile
    {
        if (!($resource instanceof IFile)) {
            if (is_string($resource)) {
                $resource = new GFile(['path' => Core::resolvePath($resource)]);
            } else if (is_array($resource)) {
                $resource = new GFile($resource);
            } else {
                throw self::InvalidResourceException(['resource' => $resource]);
            }
        }
        return $resource;
    }

    /**
     * Возвращает ссылку на опуликованный в html ресурс
     *
     * @param mixed $resource
     * @param array $options
     * @param bool $compile
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function publicate($resource, array $options = [], bool $compile = false): string
    {
        //$resource = $this->prepareResource($resource);
        $result = '';
        foreach($this->resources as $resourceName) {
            if ($this->p($resourceName)->isAllowedResource($resource)) {
                $result = $this->p($resourceName)->publicate($resource, $options, $compile);
                break;
            }
        }
        return $result;
    }

    /**
     * Устанавливает список плагинов-публикаторов ресурсов
     *
     * @param array $resources
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setResources(array $resources)
    {
        $this->_resources = $resources;
    }

    /**
     * Установка название плагина для работы с кэшем
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCacheName(string $name)
    {
        $this->_cacheName = $name;
    }
}