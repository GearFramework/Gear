<?php

namespace Gear\Modules\Resources;

use Gear\Core;
use Gear\Interfaces\IFile;
use Gear\Library\GModule;
use Gear\Library\Io\Filesystem\GFile;

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
                'class' => '\Gear\Modules\Resources\Plugins\GJsResourcesPlugin',
                'allowedExtensions' => ['js'],
            ],
            'css' => [
                'class' => '\Gear\Modules\Resources\Plugins\GCssResourcesPlugin',
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
        foreach ($this->resources as $resourceName) {
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
    public function getCache(): ?ICache
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
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function prepareResource($resource): IFile
    {
        if (!($resource instanceof IFile)) {
            if (is_string($resource)) {
                $resource = new GFile(['path' => Core::resolvePath($resource)]);
            } elseif (is_array($resource)) {
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
        $result = '';
        if (is_array($resource)) {
            $result = [];
            foreach ($resource as $res) {
                $result[] = $this->publicate($res, $options, $compile);
            }
            $result = implode("\n", $result);
        } else {
            foreach ($this->resources as $resourceName) {
                if ($this->p($resourceName)->isAllowedResource($resource)) {
                    $result = $this->p($resourceName)->publicate($resource, $options, $compile);
                    break;
                }
            }
        }
        return $result;
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
}
