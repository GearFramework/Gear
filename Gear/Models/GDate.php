<?php

namespace Gear\Models;

use Gear\Helpers\GCalendaOptions;
use Gear\Library\GModel;

/**
 * Модель даты
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GDate extends GModel
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'options' => [
            'format' => 'Y-m-d H:i:s',
        ]
    ];
    protected $_options = null;
    /* Public */

    /**
     * Возвращает отформатированную дату и время
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __toString(): string
    {
        return $this->date();
    }

    /**
     * Обработка переданных опциональных значений
     *
     * @param array|GCalendaOptions $options
     * @return GCalendaOptions
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _prepareOptions($options): GCalendaOptions
    {
        if ($options instanceof GCalendaOptions) {
            $options->props(array_replace_recursive(self::$_config['options'], $options->props()));
        } else {
            if (is_array($options)) {
                $options = array_replace_recursive(self::$_config['options'], $options);
            } else {
                $options = self::$_config['options'];
            }
            $options = new GCalendaOptions($options);
        }
        $this->options = $options;
        return $options;
    }

    public function date($options = []): string
    {
        return $this->getDate($options);
    }

    public function getDate($options = []): string
    {
        $this->options = $this->_prepareOptions($options);
        return date($options->format, $this->timestamp);
    }

    public function getDay(): int
    {
        return date('j', $this->timestamp);
    }

    /**
     * Возвращает месяц
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMonth(): int
    {
        return date('n', $this->timestamp);
    }

    /**
     * Возвращает текущие опции
     *
     * @return GCalendaOptions
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOptions(): GCalendaOptions
    {
        return $this->_options;
    }

    public function getTimestamp(): int
    {
        return $this->props('timestamp');
    }

    public function getYear(): int
    {
        return date('Y', $this->timestamp);
    }

    public function month(): int
    {
        return $this->getMonth();
    }

    public function setOptions($options)
    {
        $this->_prepareOptions($options);
    }

    /**
     * Возвращает unix timestamp
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function timestamp(): int
    {
        return $this->getTimestamp();
    }

    /**
     * Возвращает год
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function year(): int
    {
        return $this->getYear();
    }
}
