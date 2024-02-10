<?php

namespace Gear\Library\Services;

use Gear\Interfaces\Services\ComponentContainedInterface;
use Gear\Interfaces\Services\ModuleInterface;
use Gear\Interfaces\Services\PluginContainedInterface;
use Gear\Traits\Services\ComponentContainedTrait;
use Gear\Traits\Services\PluginContainedTrait;

/**
 * Класс модулей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class Module extends Service implements ModuleInterface, ComponentContainedInterface
{
    /* Traits */
    use ComponentContainedTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
