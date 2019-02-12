<?php

namespace Gear\Library;
use Gear\Interfaces\ModelInterface;
use Gear\Interfaces\ObjectInterface;
use Gear\Traits\PluginContainedTrait;

/**
 * Базовый класс моделей
 *
 * @package Gear Framework
 *
 * @property iterable plugins
 * @property mixed primaryKey
 * @property string primaryKeyName
 * @property iterable registeredPlugins
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GModel extends GObject implements ModelInterface
{
    /* Traits */
    use ServiceContainedT;
    use PluginContainedTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public static $primaryKeyName = 'id';

    /**
     * GModel constructor.
     *
     * @param array|\Closure $properties
     * @param null|ObjectInterface $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct($properties = [], ObjectInterface $owner = null)
    {
        parent::__construct($properties, $owner);
    }
}
