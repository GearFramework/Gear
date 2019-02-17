<?php

use Gear\Library\GException;

/**
 * Базовое исключение шаблонизатора
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class ViewerException extends GException {}

/**
 * Исключение при ошибках шаблона
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class InvalidTemplateException extends ViewerException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = 'Invalid template <{template}>';
}
