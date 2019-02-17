<?php

use Gear\Library\GException;

/**
 * Базовые исключения при работе с кэшем
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class CacheException extends GException {}

/**
 * Исключение, возникающее при работе с кэш-сервером
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class CacheInvalidServerException extends CacheException
{
    public $defaultMessage = "Invalid cache server";
}

