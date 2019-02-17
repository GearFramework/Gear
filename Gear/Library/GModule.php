<?php

namespace Gear\Library;

use Gear\Interfaces\ModuleInterface;
use Gear\Traits\ComponentContainedTrait;
use Gear\Traits\PluginContainedTrait;
use Gear\Traits\ServiceContainedTrait;

/**
 * Класс модулей
 *
 * @package Gear Framework
 *
 * @property iterable components
 * @property iterable plugins
 * @property iterable registeredComponents
 * @property iterable registeredPlugins
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GModule extends GService implements ModuleInterface
{
    /* Traits */
    use ServiceContainedTrait;
    use ComponentContainedTrait;
    use PluginContainedTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
