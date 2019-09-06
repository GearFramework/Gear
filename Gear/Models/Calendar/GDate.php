<?php

namespace Gear\Models\Calendar;

use Gear\Core;
use Gear\Library\Calendar\GCalendarOptions;
use Gear\Library\Calendar\GLocale;
use Gear\Library\GModel;
use MongoDB\BSON\Timestamp;

/**
 * Модель даты
 *
 * @package Gear Framework
 *
 * @property string date
 * @property int day
 * @property int dayWeek
 * @property int dayYear
 * @property int hours
 * @property string iso
 * @property GLocale locale
 * @property int minutes
 * @property int month
 * @property GCalendarOptions options
 * @property int quarter
 * @property string rfc
 * @property int seconds
 * @property int timestamp
 * @property int week
 * @property int year
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
    protected $_options = [
        'format' => 'Y-m-d H:i:s',
    ];
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
                $options = $this->_options;
            }
            $options = new GCalendarOptions($options);
        }
        $this->_options = $options;
        return $options;
    }

    /**
     * Вызывается после создания объекта
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterConstruct()
    {
        date_default_timezone_set(Core::props('timezone'));
        return parent::afterConstruct();
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
     * Возвращает день месяца
     *
     * @param string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function day(string $format = '')
    {
        if ($format) {
            return date($format, $this->timestamp);
        } else {
            return $this->getDay();
        }
    }

    /**
     * Возвращает разницу между датами
     *
     * @param int|string|GDate $date
     * @return GTimeInterval
     * @since 0.0.1
     * @version 0.0.1
     */
    public function diff($date): GTimeInterval
    {
        /** @var GDate $date */
        $date = \Calendar::date($date);
        return new GTimeInterval(['interval' => abs($this->timestamp - $date->timestamp)]);
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
        return $this->locale->format((int)$this->timestamp, $this->options);
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

    /**
     * Возвращает порядковый день недели
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDayWeek(): int
    {
        return date('w', $this->timestamp);
    }

    /**
     * Возвращает порядковый день года
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDayYear(): int
    {
        return date('z', $this->timestamp);
    }

    /**
     * Возвращает часы в 24-ом формате без ведущего нуля
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHours(): int
    {
        return (int)date('G', $this->timestamp);
    }

    /**
     * Возвращает дату в формает iso
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIso(): string
    {
        return date('c', $this->timestamp);
    }

    /**
     * Возвращает локаль
     *
     * @return null|GLocale
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getLocale(): ?GLocale
    {
        if (!($this->_locale instanceof GLocale)) {
            $this->locale = \Calendar::getLocale();
        }
        return $this->_locale;
    }

    /**
     * Возвращает минуты без ведущего нуля
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMinutes(): int
    {
        return (int)date('i');
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
     * Возвращает следующую дату
     *
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getNextDate(): GDate
    {
        return \Calendar::makeDate($this->hours, $this->minutes, $this->seconds, $this->day + 1, $this->month, $this->year);
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
        if (is_array($this->_options)) {
            $this->_prepareOptions([]);
        }
        return $this->_options;
    }

    /**
     * Возвращает предыдущую дату
     *
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrevDate(): GDate
    {
        return \Calendar::makeDate($this->hours, $this->minutes, $this->seconds, $this->day - 1, $this->month, $this->year);
    }

    /**
     * Возвращает номер квартала
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getQuarter(): int
    {
        return ceil($this->month / 3);
    }

    /**
     * Возвращает дату в формате RFC 2822
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRfc(): string
    {
        return date('r', $this->timestamp);
    }

    /**
     * Возвращает секунды без ведущего нуля
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSeconds(): int
    {
        return (int)date('s');
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
     * Возвращает порядковый номер недели в году
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getWeek(): int
    {
        return date('W', $this->timestamp);
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
     * Возвращает часы
     *
     * @param string $format
     * @return false|int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function hours(string $format = '')
    {
        if ($format) {
            return date($format, $this->timestamp);
        } else {
            return $this->getHours();
        }
    }

    /**
     * Возвращает true, если год високосный
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isLeap(): bool
    {
        return \Calendar::isLeap($this);
    }

    /**
     * Возвращает дату в формает iso
     *
     * @param bool $withoutGMT
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function iso(bool $withoutGMT = false): string
    {
        if ($withoutGMT) {
            return date('Y-m-d', $this->timestamp) . 'T' . date('H:i:s', $this->timestamp);
        } else {
            return $this->getIso();
        }
    }

    /**
     * Возвращает минуты
     *
     * @param string $format
     * @return false|int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function minutes(string $format = '')
    {
        if ($format) {
            return date($format, $this->timestamp);
        } else {
            return $this->getMinutes();
        }
    }

    /**
     * Возвращает месяц
     *
     * @param string $format
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function month(string $format = '')
    {
        if ($format) {
            $options = clone $this->_prepareOptions();
            $options->format = $format;
            return $this->locale->format((int)$this->timestamp, $options);
        } else {
            return $this->getMonth();
        }
    }

    /**
     * Возвращает следующую дату
     *
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public function nextDate(): GDate
    {
        return $this->getNextDate();
    }

    /**
     * Возвращает предыдущую дату
     *
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public function prevDate(): GDate
    {
        return $this->getPrevDate();
    }

    /**
     * Возвращает номер квартала
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function quarter(): int
    {
        return $this->getQuarter();
    }

    /**
     * Возвращает дату в формате RFC 2822
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function rfc(): string
    {
        return $this->getRfc();
    }

    /**
     * Возвращает секунды
     *
     * @param string $format
     * @return false|int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function seconds(string $format = '')
    {
        if ($format) {
            return date($format, $this->timestamp);
        } else {
            return $this->getHours();
        }
    }

    /**
     * Установка дня месяца
     *
     * @param int $day
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDay(int $day)
    {
        $this->timestamp = mktime($this->hours, $this->minutes, $this->seconds, $this->month, $day, $this->year);
    }

    /**
     * Установка текущекй локали
     *
     * @param null|GLocale $locale
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setLocale(?GLocale $locale)
    {
        $this->_locale = $locale;
    }

    /**
     * Установка месяца
     *
     * @param int $month
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setMonth(int $month)
    {
        $this->timestamp = mktime($this->hours, $this->minutes, $this->seconds, $month, $this->day, $this->year);
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
     * Установка года
     *
     * @param int $year
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setYear(int $year)
    {
        $this->timestamp = mktime($this->hours, $this->minutes, $this->seconds, $this->month, $this->day, $year);
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
     * Возвращает порядковый день недели или значение в указанному формате
     *
     * @param string $format
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function week(string $format = '')
    {
        if ($format) {
            $options = clone $this->_prepareOptions();
            $options->format = $format;
            return $this->locale->format((int)$this->timestamp, $options);
        } else {
            return $this->getWeek();
        }
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
