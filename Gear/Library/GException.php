<?php

namespace Gear\Library;

use Gear\Core;

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
    protected static $_localeSection = 'Exceptions';
    /* Public */
    public $defaultMessage = "Exception message";

    /**
     * GException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param array $context
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, array $context = [])
    {
        if (empty($message)) {
            $message = $this->defaultMessage;
        }
        $message = Core::lang()->tr($message, self::getLocaleSection());
        foreach ($context as $name => $value) {
            $message = str_replace('{' . $name . '}', $value, $message);
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
