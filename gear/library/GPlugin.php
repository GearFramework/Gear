<?php

namespace gear\library;

use gear\interfaces\IPlugin;

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
    /**
     * @var bool $_initialized содержит состояние инициализации класса сервиса
     */
    protected static $_initialized = false;
    /* Public */
}