<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;
use Gear\Traits\ObjectTrait;
use Gear\Traits\PluginContainedTrait;
use Gear\Traits\PropertiesTrait;
use Gear\Traits\EventTrait;
use Gear\Traits\ServiceContainedTrait;
use Gear\Traits\ViewTrait;

/**
 * Базовый класс объектов
 *
 * @package Gear Framework
 *
 * @property int access
 * @property array events
 * @property string namespace
 * @property ObjectInterface|null owner
 * @property array properties
 * @property string viewerName
 * @property string viewPath
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GObject implements ObjectInterface
{
    /* Traits */
    use EventTrait;
    use ObjectTrait;
    use PluginContainedTrait;
    use PropertiesTrait;
    use ServiceContainedTrait;
    use ViewTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    /**
     * @var array $_config конфигурация класса
     */
    protected static $_config = [
        'plugins' => [
            'view' => ['class' => '\Gear\Plugins\Templater\GViewerPlugin']
        ]
    ];
    /**
     * @var array $_defaultProperties значения по-умолчанию для объектов класса
     */
    protected static $_sleepProperties = ['access', 'owner'];
    /**
     * @var int $_access режим доступа к объекту
     */
    protected $_access = Core::ACCESS_PUBLIC;
    /**
     * @var array $_events события класса и их обработчики
     */
    protected $_events = [];
    /**
     * @var null|string пространство имён класса объекта
     */
    protected $_namespace = null;
    /**
     * @var array $_properties свойства объектов
     */
    protected $_properties = [];
    /* Public */

    /**
     * Клонирование объекта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __clone() {}
}
