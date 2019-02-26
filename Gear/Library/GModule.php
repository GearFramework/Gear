<?php

namespace Gear\Library;

use Gear\Interfaces\ComponentContainedInterface;
use Gear\Interfaces\ModuleInterface;
use Gear\Traits\ComponentContainedTrait;
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
class GModule extends GService implements ModuleInterface, ComponentContainedInterface
{
    /* Traits */
    use ServiceContainedTrait;
    use ComponentContainedTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
