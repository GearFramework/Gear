<?php

namespace Gear\Library;

use Gear\Interfaces\IPlugin;

/**
 * Базовый класс плагинов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GPlugin extends GService implements IPlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [];
    protected static $_isInitialized = false;
    /* Public */
}