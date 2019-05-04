<?php

namespace Gear\Components\International;

use Gear\Core;
use Gear\Interfaces\InternationalInterface;
use Gear\Library\GComponent;

/**
 * Сервис-компонент интернационализации
 *
 * @package Gear Framework
 *
 * @property array messages
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GInternationalComponent extends GComponent implements InternationalInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_messages = [];
    /* Public */

    /**
     * Добавление новой секции сообщений
     *
     * @param string $section
     * @param array $messages
     * @since 0.0.1
     * @version 0.0.1
     */
    public function attachSection(string $section, array $messages)
    {
        $this->_messages[$section] = $messages;
    }

    /**
     * Возвращает массив сообщений, если указано название секции, то возвращает массив сообщений из указанной
     * секции сообщений
     *
     * @param string $section
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMessages(string $section = ''): array
    {
        if (!$section) {
            return $this->_messages;
        } else {
            return isset($this->_messages[$section]) ? $this->_messages[$section] : [];
        }
    }

    /**
     * Возвращает перевод указанного сообщения
     *
     * @param string $message
     * @param string $section
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function tr(string $message, string $section = ''): string
    {
        if (!($messages = $this->getMessages($section))) {
            $path = $section = Core::resolvePath('International\\' . ($section ? $section . '\\' : '') . Core::props('locale') . '.php');
            $messages = [];
            if (file_exists($path)) {
                $messages = require $path;
                $this->attachSection($section, $messages);
            }
        }
        return isset($messages[$message]) ? $messages[$message] : $message;
    }
}
