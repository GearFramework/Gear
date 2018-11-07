<?php

namespace Gear\Library;

use Gear\Interfaces\IModule;
use Gear\Traits\TComponentContained;
use Gear\Traits\TPluginContained;
use Gear\Traits\TServiceContained;

/**
 * Класс модулей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GModule extends GService implements IModule
{
    /* Traits */
    use TServiceContained;
    use TComponentContained;
    use TPluginContained;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
