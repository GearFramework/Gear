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
    /* Public */

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
                throw self::exceptionInvalidResource(['resource' => $resource]);
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
        $resource = $this->prepareResource($resource);
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
}