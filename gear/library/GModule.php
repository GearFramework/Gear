<?php

namespace gear\library;

use gear\interfaces\IModule;
use gear\traits\TBehaviorContained;
use gear\traits\TComponentContained;
use gear\traits\TPluginContained;

/**
 * Базовый класс модулей
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
    use TBehaviorContained;
    use TPluginContained;
    use TComponentContained;
    /* Const */
    /* Private */
    /* Protected */
    /**
     * @var bool $_initialized содержит состояние инициализации класса сервиса
     */
    protected static $_initialized = false;
    /* Public */
}
