<?php

namespace Gear\Library;

use Gear\Interfaces\ComponentInterface;
use Gear\Interfaces\PluginContainedInterface;
use Gear\Traits\PluginContainedTrait;
use Gear\Traits\ServiceContainedTrait;

/**
 * Класс компонентов
 *
 * @package Gear Framework
 *
 * @property iterable plugins
 * @property iterable registeredPlugins
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GComponent extends GService implements ComponentInterface, PluginContainedInterface
{
    /* Traits */
    use ServiceContainedTrait;
    use PluginContainedTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
