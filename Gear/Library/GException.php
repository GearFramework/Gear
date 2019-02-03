<?php

namespace Gear\Library;

use Gear\Core;
use Throwable;

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
     * @param array $context
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, array $context = [])
    {
        if (empty($message)) {
            $message = $this->defaultMessage;
        }
        $message = $this->_buildMessage($message, $context);
        if (Core::isInitialized() === true && Core::isComponentRegistered(Core::props('international'))) {
            $international = Core::service(Core::props('international'));
            $message = $international->tr($message, self::getLocaleSection());
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * Создание сообщения на основе параметров контекста
     *
     * @param string $message
     * @param array $context
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _buildMessage(string $message, array $context): string
    {
        foreach ($context as $name => $value) {
            $message = str_replace('{' . $name . '}', $value, $message);
        }
        return $message;
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
