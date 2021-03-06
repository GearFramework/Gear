<?php

namespace Gear\Modules\Resources;

use Gear\Core;
use Gear\Interfaces\CacheInterface;
use Gear\Interfaces\FileInterface;
use Gear\Library\GModule;
use Gear\Library\Io\Filesystem\GFile;

/**
 * Менеджер публикации пользовательских ресурсов
 *
 * @package Gear Framework
 *
 * @property string cacheName
 * @property array resources
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GResourcesModule extends GModule
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static array $_config = [
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
     * @throws \PluginNotFoundException
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
     * @return null|CacheInterface
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCache(): ?CacheInterface
    {
        /**
         * @var CacheInterface $cache
         */
        $cache = null;
        if ($this->isPluginRegistered($this->cacheName)) {
            $cache = $this->p($this->cacheName);
        }
        return $cache;
    }

    /**
     * Возвращает название плагина для работы с кэшем или null, таковой не зарегистрирован
     *
     * @return null|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCacheName(): ?string
    {
        return $this->_cacheName;
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
     * @param string|FileInterface $resource
     * @return FileInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function prepareResource($resource): FileInterface
    {
        if (!($resource instanceof FileInterface)) {
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
     * @throws \PluginNotFoundException
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
