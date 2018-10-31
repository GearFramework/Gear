<?php

namespace Gear\Models\Calendar;

use Gear\Library\Calendar\GCalendarOptions;
use Gear\Library\Calendar\GLocale;
use Gear\Library\GModel;

/**
 * Модель даты
 *
 * @package Gear Framework
 *
 * @property GLocale locale
 * @property GCalendarOptions options
 * @property int timestamp
 *
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
    protected $_timestamp = 0;
    protected $_locale = null;
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
        return $this->getDate();
    }

    /**
     * Обработка переданных опциональных значений
     *
     * @param array|string|GCalendarOptions $options
     * @return GCalendarOptions
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _prepareOptions($options): GCalendarOptions
    {
        if ($options instanceof GCalendarOptions) {
            $options->props(array_replace_recursive(self::$_config['options'], $options->props()));
        } else {
            if (is_array($options)) {
                $options = array_replace_recursive(self::$_config['options'], $options);
            } elseif (is_string($options)) {
                $options = ['format' => $options];
            } else {
                $options = self::$_config['options'];
            }
            $options = new GCalendarOptions($options);
        }
        $this->_options = $options;
        return $options;
    }

    /**
     * Возвращает отформатированную дату и время
     *
     * @param array|string|GCalendarOptions $options
     * @return string
     * @use $this->getDate()
     * @since 0.0.1
     * @version 0.0.1
     */
    public function date($options = []): string
    {
        return $this->getDate($options);
    }

    /**
     * Устанавливает формат отображения даты
     *
     * @param string $format
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    public function format(string $format)
    {
        static::$_config['options']['format'] = $format;
        return $this;
    }

    /**
     * Возвращает отформатированную дату и время
     *
     * @param array|string|GCalendarOptions $options
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDate($options = []): string
    {
        $this->options = $options = $this->_prepareOptions($options);
        return date($options->format, $this->timestamp);
    }

    /**
     * Возвращает день месяца без ведущего нуля
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDay(): int
    {
        return date('j', $this->timestamp);
    }

    public function getIso(): string
    {
        return date('Y-m-d', $this->timestamp) . 'T' . date('H:i:s', $this->timestamp);
    }

    /**
     * Возвращает месяц без ведущего нуля
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
     * @return null|GCalendarOptions
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOptions(): ?GCalendarOptions
    {
        return $this->_options;
    }

    /**
     * Возвращает UNIX Timestamp
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getTimestamp(): int
    {
        return $this->_timestamp;
    }

    /**
     * Возвращает год
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getYear(): int
    {
        return date('Y', $this->timestamp);
    }

    /**
     * Возвращает месяц без ведущего нуля
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function month(): int
    {
        return $this->getMonth();
    }

    /**
     * Установка текущекй локали
     *
     * @param GLocale $locale
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setLocale(GLocale $locale)
    {
        $this->_locale = $locale;
    }

    /**
     * Установка параметров
     *
     * @param string|array|GCalendarOptions $options
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setOptions($options)
    {
        $this->_prepareOptions($options);
    }

    /**
     * Установка UNIX Timestamp
     *
     * @param int $timestamp
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setTimestamp(int $timestamp)
    {
        $this->_timestamp = $timestamp;
    }

    /**
     * Возвращает Unix Timestamp
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
