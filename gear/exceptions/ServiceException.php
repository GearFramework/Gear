<?php

use gear\library\GException;

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

class ComponentException extends ServiceException {}
class ComponentNotFoundException extends ComponentException {}
