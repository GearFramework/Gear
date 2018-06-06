<?php

use Gear\Library\GException;

/**
 * Базовые исключения сервисов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ServiceException extends GException {}

/**
 * Исключение при инициализации класса сервиса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ServiceInitException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "Error initializing service";
}

/**
 * Исключение при создании экземпляра сервиса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ServiceConstructException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "Error creating service";
}
