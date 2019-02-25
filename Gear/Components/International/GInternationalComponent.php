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
     * @version 0.0.1
     */
    public function tr(string $message, string $section = ''): string
    {
        $section = Core::resolvePath('International\\' . $section . '\\' . Core::props('locale'));
        if (!preg_match('/(\\\\|\/)/', $section)) {
            $section = '\Gear\International\\' . $section;
        } else {
            $section = (Core::isModuleInstalled('app') ? Core::app()->namespace : '\Gear') . '\International\\' . $section . '\\' . Core::props('locale');
        }
        if (!($messages = $this->getMessages($section))) {
            $sectionFileMessages = Core::resolvePath($section, !Core::isComponentInstalled(Core::props('loaderName'))) . '.php';
            $messages = [];
            if (file_exists($sectionFileMessages)) {
                $messages = require($sectionFileMessages);
            }
            $this->attachSection($section, $messages);
        }
        $messages = $this->getMessages($section);
        return isset($messages[$message]) ? $messages[$message] : $message;
    }
}
