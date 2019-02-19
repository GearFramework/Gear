<?php

use Gear\Library\GException;

/**
 * Базовые исключения компонетнта логирования
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class LogException extends GException {}

/**
 * Исключение, возникающее когда не найден плагин для записи данных логирования
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class InvalidLogWriterException extends LogException
{
    public $defaultMessage = "Invalid log-writer <{logger}>";
}
