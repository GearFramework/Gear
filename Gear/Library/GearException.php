<?php

namespace Gear\Library;

use Exception;
use Throwable;

/**
 * Класс исключений фреймворка
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class GearException extends Exception
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected string $defaultMessage = '';
    /* Public */

    /**
     * GearException constructor.
     *
     * @param string            $message
     * @param int               $code
     * @param Throwable|null    $previous
     * @param array             $context
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null, array $context = [])
    {
        if (empty($message)) {
            $message = $this->defaultMessage;
        }
        //$message = Core::lang()->tr($message, self::getLocaleSection());
        foreach ($context as $name => $value) {
            $message = str_replace('{' . $name . '}', $value, $message);
        }
        parent::__construct($message, $code, $previous);
    }
}
