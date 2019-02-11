<?php

namespace Gear\Library;

use Throwable;

/**
 * Класс исключений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GException extends \Exception
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_localeSection = 'exceptions';
    /* Public */
    public $defaultMessage = "Exception message";

    /**
     * GException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = $this->defaultMessage;
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * Возвращает название секции, в которой лежат файлы с переводом сообщений на другие языки
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getLocaleSection(): string
    {
        return static::$_localeSection;
    }
}
