<?php

namespace Gear\Library;

use Gear\Interfaces\IComponent;
use Gear\Traits\TPluginContained;
use Gear\Traits\TServiceContained;

/**
 * Класс компонентов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GComponent extends GService implements IComponent
{
    /* Traits */
    use TServiceContained;
    use TPluginContained;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
