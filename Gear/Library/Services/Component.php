<?php

namespace Gear\Library;

use Gear\Interfaces\ComponentInterface;
use Gear\Interfaces\PluginContainedInterface;

/**
 * Класс компонентов
 *
 * @package Gear Framework 2
 *
 * @property iterable plugins
 * @property iterable registeredPlugins
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
class Component extends Service implements ComponentInterface, PluginContainedInterface
{
    /* Traits */
    use ServiceContainedTrait;
    use PluginContainedTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
