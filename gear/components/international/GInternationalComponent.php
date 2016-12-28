<?php

namespace gear\components\international;

use gear\Core;
use gear\library\GComponent;

/**
 * Сервис-компонент интернационализации
 *
 * @property array messages
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GInternationalComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_messages = [];
    /* Public */

    /**
     * Возвращает перевод указанного сообщения
     *
     * @param string $message
     * @param string $section
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function tr(string $message, string $section): string
    {
        if (!preg_match('/(\\\\|\/)/', $section)) {
            $section = '\gear\locales\\' . $section;
        } else {
            $section = (Core::isModuleInstalled('app') ? Core::app()->namespace : '\gear') . '\locales\\' . $section . '\\' . Core::props('locale');
        }
        if (!($messages = $this->getMessages($section))) {
            $sectionFileMessages = Core::resolvePath($section, !self::isComponentInstalled(Core::props('loaderName'))) . '.php';
            $messages = [];
            if (file_exists($sectionFileMessages)) {
                $messages = require $sectionFileMessages;
            }
            $this->attachSection($section, $messages);
        }
        $messages = $this->getMessages($section);
        return isset($messages[$message]) ? $messages[$message] : $message;
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
        return !$section ? $this->_messages : (isset($this->_messages[$section]) ? $this->_messages[$section] : []);
    }

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
}
