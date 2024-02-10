<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;
use Gear\Interfaces\PluginContainedInterface;
use Gear\Interfaces\ViewableInterface;
use Gear\Traits\ObjectTrait;
use Gear\Traits\PluginContainedTrait;
use Gear\Traits\PropertiesTrait;
use Gear\Traits\EventTrait;
use Gear\Traits\ServiceContainedTrait;
use Gear\Traits\ViewTrait;

/**
 * Базовый класс объектов
 *
 * @package Gear Framework 2
 *
 * @property int access
 * @property array events
 * @property string namespace
 * @property ObjectInterface|null owner
 * @property array properties
 * @property string viewerName
 * @property string viewPath
 * @property array viewsMap
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
class Object implements ObjectInterface, PluginContainedInterface, ViewableInterface
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
    /** @var array $_config конфигурация класса */
    protected static array $_config = [
        'plugins' => [
            'view' => ['class' => '\Gear\Plugins\Templater\GViewerPlugin']
        ]
    ];
    /** @var array $_defaultProperties значения по-умолчанию для объектов класса */
    protected static array $_sleepProperties = ['access', 'owner'];
    /** @var int $_access режим доступа к объекту */
    protected int $_access = Core::ACCESS_PUBLIC;
    /** @var array $_events события класса и их обработчики */
    protected array $_events = [];
    /** @var null|string пространство имён класса объекта */
    protected ?string $_namespace = null;
    /** @var null|ObjectInterface владелец объекта */
    protected ?ObjectInterface $_owner = null;
    /** @var array $_properties свойства объектов */
    protected array $_properties = [];
    /** @var array $_viewsMap карта шаблонов для шалонизатора */
    protected array $_viewsMap = [];
    /* Public */

    /**
     * Клонирование объекта
     *
     * @since 0.0.1
     * @version 2.0.0
     */
    protected function __clone() {}
}
