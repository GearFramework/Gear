<?php

namespace gear\library;

/**
 * Класс исключений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GException extends \Exception
{
    /* Traits */
    /* Const */
    /* Private */
    private $_defaultMessage = 'Exception message';
    /* Protected */
    /* Public */

    public function __construct(string $message, int $code = 0, Exception $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
    }
}
