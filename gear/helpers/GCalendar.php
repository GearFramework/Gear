<?php

namespace gear\helpers;

use gear\Core;
use gear\library\GObject;
use gear\interfaces\IStaticFactory;
use gear\traits\TStaticFactory;

define('AS_NUMERIC', 1);
define('AS_FULLNAME', 2);
define('AS_SHORTNAME', 3);

/**
 * Класс для работы с календарём
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.06.2014
 * @php 5.4.x or higher
 */
class GCalendar extends GObject implements IStaticFactory
{
    /* Traits */
    use TStaticFactory;
    /* Const */
    const SECONDS_PER_MINUTE = 60;
    const SECONDS_PER_HOUR = 3600;
    const SECONDS_PER_DAY = 86400;
    const DAYS_PER_WEEK = 7;
    const AS_NUMERIC = 1;
    const AS_FULLNAME = 2;
    const AS_SHORTNAME = 3;
    /* Private */
    /* Protected */
    protected static $_factory = [
        'class' => [
            'name' => '\gear\models\GDate',
            'ownerCalendar' => __CLASS__,
        ],
        'format' => 'Y-m-d H:i:s',
        'natural' => false,
    ];
    protected static $_current = null;
    protected static $_locale = 'ru_RU';
    protected static $_localeNamespace = '\gear\helpers\locales';
    protected static $_format = 'Y-m-d H:i:s';
    protected static $_natural = false;
    /* Public */

    /**
     * Возвращает объект даты, указанной в качестве названия метода.
     * Пример
     *    GCalendar::{'2015-06-29'}();
     *
     * @access public
     * @static
     * @param string $name
     * @param array $args
     * @return \gear\models\GDate
     * @php => 5.4
     */
    public static function __callStatic($name, $args)
    {
        if (preg_match('/^exception[A-Z]/', $name))
            return call_user_func_array(['\gear\Core', $name], $args);
        else
        if (preg_match('/^on[A-Z]/', $name) && method_exists(get_called_class(), 'trigger'))
        {
            array_unshift($args, $name);
            return call_user_func_array(['\gear\Core', 'trigger'], $args);
        }
        return self::getDate($name);
    }

    /**
     * Текущая дата и время
     *
     * @access public
     * @return \gear\models\GDate
     */
    public static function now()
    {
        return self::factory(['timestamp' => time()]);
    }

    /**
     * Возвращает завтрашнюю дату
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @return \gear\models\GDate
     */
    public static function tomorrow()
    {
        return self::factory(['timestamp' => time() + self::SECONDS_PER_DAY]);
    }

    /**
     * Возвращает вчерашнюю дату
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @return \gear\models\GDate
     */
    public static function yesterday()
    {
        return self::factory(['timestamp' => time() - self::SECONDS_PER_DAY]);
    }

    /**
     * Следующий день, относительно указанного в параметре Если параметр $date Не указан, то будет
     * использоваться текущий день
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function nextDay($date = null)
    {
        return self::addDay($date);
    }

    /**
     * Предыдущий день, относительно указанного в параметре. Если параметр $date Не указан, то будет
     * использоваться текущий день
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function previousDay($date = null)
    {
        return static::subDay($date);
    }

    /**
     * Следующий месяц
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function nextMonth($date = null)
    {
        return static::addMonth($date);
    }

    /**
     * Предыдущий месяц
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function previousMonth($date = null)
    {
        return static::subMonth($date);
    }

    /**
     * Следующий год
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function nextYear($date = null)
    {
        return static::addYear($date);
    }

    /**
     * Предыдущий год
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function previousYear($date = null)
    {
        return static::subYear($date);
    }

    /**
     * Следующий час
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function nextHour($date = null)
    {
        return static::addHour($date);
    }

    /**
     * Предыдущий час
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function previousHour($date = null)
    {
        return static::subHour($date);
    }

    /**
     * Следующая минута
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function nextMinute($date = null)
    {
        return static::addMinute($date);
    }

    /**
     * Предыдущая минута
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function previousMinute($date = null)
    {
        return static::subMinute($date);
    }

    /**
     * Следующая секунда
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function nextSecond($date = null)
    {
        return static::addSecond($date);
    }

    /**
     * Предыдущая секунда
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function previousSecond($date = null)
    {
        return static::subSecond($date);
    }

    /**
     * Следующая неделя
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function nextWeek($date = null)
    {
        return static::addDays($date, 7);
    }

    /**
     * Предыдущая неделя
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function previousWeek($date = null)
    {
        return static::subDays($date, 7);
    }

    /**
     * Установка даты в календаре
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function setCurrent($date)
    {
        return static::setDate($date);
    }

    /**
     * Возвращает установленную дату в календаре (по-умолчанию текущая дата)
     *
     * @access public
     * @return \gear\models\GDate
     */
    public static function current() { return static::getCurrent(); }
    public static function getCurrent()
    {
        return static::$_current ?: static::$_current = static::now();
    }

    /**
     * Установка текущей даты календаря
     * Параметр $date может принимать объект класса \gear\models\GDate или относледованных от него, строку даты,
     * которая воспринимается функцией strtotime(), числовое значение timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function setDate($date)
    {
        if (!is_object($date))
            $date = static::factory(['timestamp' => is_numeric($date) ? $date : strtotime($date)]);
        return static::setCurrent($date);
    }

    /**
     * Возвращает текущую дату календаря или создаёт новую по указанному
     * значению в параметре $date
     * Параметр $date может принимать строку даты, которая воспринимается функцией strtotime() или
     * числовое значение timestamp
     *
     * @access public
     * @param null|integer|string $date
     * @return \gear\models\GDate
     */
    public static function date($date = null) { return static::getDate($date); }
    public static function getDate($date = null)
    {
        if (!$date)
            $date = static::getCurrent();
        else
        if (is_numeric($date) || is_string($date))
            $date = static::factory(['timestamp' => is_numeric($date) ? $date : strtotime($date)]);
        if (!is_object($date))
            throw self::exceptionInvalidDate(['date' => $date]);
        return $date;
    }

    /**
     * Установка UNIX-timestamp
     *
     * @access public
     * @param integer $timestamp
     * @param null|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function setTimestamp($timestamp, $date = null)
    {
        if (!$date || !is_object($date))
            $date = static::setCurrent($timestamp);
        else
            $date = $date->setTimestamp($timestamp);
        return $date;
    }

    /**
     * Возвращает UNIX-timestamp
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function timestamp($date = null) { return self::getTimestamp($date); }
    public static function getTimestamp($date = null)
    {
        if (!$date)
            $timestamp = static::getCurrent()->timestamp;
        else
            $timestamp = static::getDate($date)->timestamp;
        return $timestamp;
    }

    /**
     * Установка числа
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $day
     * @return \gear\models\GDate
     */
    public static function setDay($date = null, $day)
    {
        $date = static::getDate($date);
        return static::setTimestamp(static::mktime($date, null, null, null, null, $day), $date);
    }

    /**
     * Возвращает число
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function day($date = null) { return static::getDay($date); }
    public static function getDay($date = null)
    {
        return date('d', $date->timestamp);
    }

    /**
     * Добавление к указанной дате один день и возвращает новую дату
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function addDay($date = null)
    {
        return static::addDays($date, 1);
    }

    /**
     * Добавление к текущей дате указанное число дней и возвращает новую дату
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $days кол-во дней, которые необходимо прибавить
     * @return \gear\models\GDate
     */
    public static function addDays($date = null, $days)
    {
        $date = static::getDate($date);
        return static::setTimestamp($date->timestamp + $days * self::SECONDS_PER_DAY, $date);
    }

    /**
     * Вычитает из указанной дате один день и возвращает новую дату
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function subDay($date = null)
    {
        return static::subDays($date, 1);
    }

    /**
     * Вычитает из текущей даты указанное число дней и возвращает новую дату
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $days кол-во дней, которые необходимо вычесть
     * @return \gear\models\GDate
     */
    public static function subDays($date = null, $days)
    {
        $date = static::getDate($date);
        return static::setTimestamp($date->timestamp - $days * self::SECONDS_PER_DAY, $date);
    }

    /**
     * Установка месяца
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $month
     * @return \gear\models\GDate
     */
    public static function setMonth($date = null, $month)
    {
        $date = static::getDate($date);
        return static::setTimestamp(static::mktime($date, null, null, null, $month), $date);
    }

    /**
     * Получение месяца
     *
     * Значения для $mode
     *
     * 1 - возращает порядковый номер месяца
     * 2 - возвращает полное название месяца
     * 3 - возвращает сокращённое название месяца
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $mode
     * @return integer
     */
    public static function month($date = null, $mode = self::AS_NUMERIC) { return static::getMonth($date, $mode); }
    public static function getMonth($date = null, $mode = self::AS_NUMERIC)
    {
        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        $timestamp = $date->timestamp;
        switch ($mode) {
            case self::AS_SHORTNAME :
                return $class::getShortMonth($timestamp);
            case self::AS_FULLNAME :
                return $class::getFullMonth($timestamp);
            case self::AS_NUMERIC :
            default :
                return date('m', $timestamp);
        }
    }

    /**
     * Прибавляет к дате один месяц
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function addMonth($date = null)
    {
        return static::addMonths($date, 1);
    }

    /**
     * Прибавляет к дате указанное количество месяцев
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $months
     * @return \gear\models\GDate
     */
    public static function addMonths($date = null, $months)
    {
        $date = static::getDate($date);
        return static::setTimestamp(strtotime('+' . (int)$months . ' month', $date->timestamp), $date);
    }

    /**
     * Вычитает из даты указанное один месяц
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function subMonth($date = null)
    {
        return static::subMonths($date, 1);
    }

    /**
     * Вычитает из даты указанное количество месяцев
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $months
     * @return \gear\models\GDate
     */
    public static function subMonths($date = null, $months)
    {
        $date = static::getDate($date);
        return static::setTimestamp(strtotime('-' . (int)$months . ' month', $date->timestamp), $date);
    }

    /**
     * Установка года
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $year
     * @return \gear\models\GDate
     */
    public static function setYear($date = null, $year)
    {
        $date = static::getDate($date);
        return static::setTimestamp(static::mktime($date, null, null, null, null, null, $year), $date);
    }

    /**
     * Возвращает год
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function year($date = null) { return static::getYear($date); }
    public static function getYear($date = null)
    {
        return date('Y', static::getDate($date)->timestamp);
    }

    /**
     * Прибавляет к дате один год
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function addYear($date = null)
    {
        return static::addYears($date, 1);
    }

    /**
     * Прибавляет к дате указанное количество лет
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $years
     * @return \gear\models\GDate
     */
    public static function addYears($date = null, $years)
    {
        $date = static::getDate($date);
        return static::setYear($date, $date->year + $years);
    }

    /**
     * Вычитает из даты один год
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function subYear($date = null)
    {
        return static::subYears($date, 1);
    }

    /**
     * Вычитает из даты указанное количество лет
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $years
     * @return \gear\models\GDate
     */
    public static function subYears($date = null, $years)
    {
        $date = static::getDate($date);
        return static::setYear($date, $date->year - $years);
    }

    /**
     * Установка часа
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $hour
     * @return \gear\models\GDate
     */
    public static function setHour($date = null, $hour)
    {
        $date = static::getDate($date);
        return static::setTimestamp(static::mktime($date, $hour), $date);
    }

    /**
     * Возвращает час
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function hour($date = null) { return self::getHour($date); }
    public static function getHour($date = null)
    {
        static::getDate($date)->hour;
    }

    /**
     * Прибавляет к времени один час
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function addHour($date = null)
    {
        return static::addHours($date, 1);
    }

    /**
     * Прибавляет к времени указанное количество часов
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $hours
     * @return \gear\models\GDate
     */
    public static function addHours($date = null, $hours)
    {
        $date = static::getDate($date);
        return static::setTimestamp($date->timestamp + $hours * self::SECONDS_PER_HOUR, $date);
    }

    /**
     * Вычитает из времени один час
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function subHour($date = null)
    {
        return static::subHours($date, 1);
    }

    /**
     * Вычитает из времени указанное количество часов
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $hours
     * @return \gear\models\GDate
     */
    public static function subHours($date = null, $hours)
    {
        $date = static::getDate($date);
        return static::setTimestamp($date->timestamp - $hours * self::SECONDS_PER_HOUR, $date);
    }

    /**
     * Установка минут
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $minute
     * @return \gear\models\GDate
     */
    public static function setMinute($date = null, $minute)
    {
        return static::setTimestamp(static::mktime($date, null, $minute), $date);
    }

    /**
     * Возвращает минуты
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function minute($date = null) { return self::getMinute($date); }
    public static function getMinute($date = null)
    {
        return date('i', $date->timestamp);
    }

    /**
     * Прибавляет к времени одну минуту
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function addMinute($date = null)
    {
        return static::addMinutes($date, 1);
    }

    /**
     * Прибавляет к времени указанное количество минут
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $minutes
     * @return \gear\models\GDate
     */
    public static function addMinutes($date = null, $minutes)
    {
        $date = static::getDate($date);
        return static::setTimestamp($date->timestamp + $minutes * self::SECONDS_PER_MINUTE, $date);
    }

    /**
     * Вычитает из времени одну минуту
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function subMinute($date = null)
    {
        return static::subMinutes($date, 1);
    }

    /**
     * Вычитает из времени указанное количество минут
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $minutes
     * @return \gear\models\GDate
     */
    public static function subMinutes($date = null, $minutes)
    {
        $date = self::getDate($date);
        return static::setTimestamp($date->timestamp - $minutes * self::SECONDS_PER_MINUTE, $date);
    }

    /**
     * Установка секунд
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $second
     * @return \gear\models\GDate
     */
    public static function setSecond($date = null, $second)
    {
        return static::setTimestamp(static::mktime($date, null, null, $second), $date);
    }

    /**
     * Возвращает секунды
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function getSecond($date = null)
    {
        return date('s', self::getDate($date)->timestamp);
    }

    /**
     * Прибавляет к времени одну секунду
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function addSecond($date = null)
    {
        return static::addSeconds($date, 1);
    }

    /**
     * Прибавляет к времени указанное количество секунд
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $seconds
     * @return \gear\models\GDate
     */
    public static function addSeconds($date = null, $seconds)
    {
        $date = self::getDate($date);
        return static::setTimestamp($date->timestamp + $seconds, $date);
    }

    /**
     * Вычитает из времени одну секунду
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function subSecond($date = null)
    {
        return static::subSeconds($date, 1);
    }

    /**
     * Вычитает из времени указанное количество секунд
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $seconds
     * @return \gear\models\GDate
     */
    public static function subSeconds($date = null, $seconds)
    {
        $date = self::getDate($date);
        return static::setTimestamp($date->timestamp - $seconds, $date);
    }

    /**
     * Прибавляет к дате одну неделю
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function addWeek($date = null)
    {
        return static::addWeeks($date, 1);
    }

    /**
     * Прибавляет к дате указанное количество недель
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $weeks
     * @return \gear\models\GDate
     */
    public static function addWeeks($date = null, $weeks)
    {
        $date = self::getDate($date);
        return static::setTimestamp($date->timestamp + $weeks * self::DAYS_PER_WEEK * self::SECONDS_PER_DAY, $date);
    }

    /**
     * Вычитает из даты одну неделю
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function subWeek($date = null)
    {
        return static::subWeeks($date, 1);
    }

    /**
     * Вычитает из даты указанное количество недель
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $weeks
     * @return \gear\models\GDate
     */
    public static function subWeeks($date = null, $weeks)
    {
        $date = self::getDate($date);
        return static::setTimestamp($date->timestamp - $weeks * self::DAYS_PER_WEEK * self::SECONDS_PER_DAY, $date);
    }

    /**
     * Возвращает день недели указанной даты
     *
     * Значения для $mode
     *
     * 1 - возращает порядковый номер дня недели
     * 2 - возвращает полное название дня недели
     * 3 - возвращает сокращённое название дня недели
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param integer $mode
     * @return integer
     */
    public static function getDayOfWeek($date = null, $mode = self::AS_NUMERIC)
    {
        $date = self::getDate($date);
        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        switch ($mode) {
            case self::AS_FULLNAME :
                return $class::getFullWeek($date->timestamp);
            case self::AS_SHORTNAME :
                return $class::getShortWeek($date->timestamp);
            case self::AS_NUMERIC :
            default :
                return $class::getNumberDayOfWeek($date->timestamp);
        }
    }

    /**
     * Возвращает номер дня в году
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function getNumberOfDay($date = null)
    {
        return date('z', self::getDate($date)->timestamp);
    }

    /**
     * Возвращает количество дней в месяце
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function getCountDaysInMonth($date = null)
    {
        return date('t', self::getDate($date)->timestamp);
    }

    /**
     * Возвращает количество дней в году
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return integer
     */
    public static function getCountDaysInYear($date = null)
    {
        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        return $class::getCountDaysInYear(self::getDate($date)->timestamp);
    }

    /**
     * GCalendar::getDaysOfWeek()
     *
     * Значения для $mode
     *
     * 1 - возращает массив порядковых номеров дней недели
     * 2 - возвращает массив полных названий дней недели
     * 3 - возвращает массив сокращённых названий дней недели
     *
     * @access public
     * @param integer $mode
     * @return array
     */
    public static function getDaysOfWeek($mode = 1)
    {
        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        switch ($mode) {
            case 2 :
                return $class::getFullWeeks();
            case 3 :
                return $class::getShortWeeks();
            case 1 :
            default :
                return $class::getNumbersDayOfWeek();
        }
    }

    /**
     * Возвращает дату, которая соответствует первому дню недели
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function getFirstDateOfWeek($date = null)
    {
        $date = self::getDate($date);
        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        $timestamp = $date->timestamp;
        $firstDayOfWeek = (int)$class::getFirstNumberDayOfWeek();
        $dayOfWeek = (int)$class::getNumberDayOfWeek($timestamp);
        if ($firstDayOfWeek >= $dayOfWeek)
            return static::getDate($timestamp);
        else
            return static::getDate($timestamp - ($dayOfWeek - $firstDayOfWeek) * self::SECONDS_PER_DAY);
    }

    /**
     * Возвращает дату, которая соответствует последнему дню недели
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function getLastDateOfWeek($date = null)
    {
        $date = self::getDate($date);
        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        $timestamp = $date->timestamp;
        $lastDayOfWeek = (int)$class::getLastNumberDayOfWeek();
        $dayOfWeek = (int)$class::getNumberDayOfWeek($timestamp);
        if ($lastDayOfWeek <= $dayOfWeek)
            return static::getDate($timestamp);
        else
            return static::getDate($timestamp + ($lastDayOfWeek - $dayOfWeek) * self::SECONDS_PER_DAY);
    }

    /**
     * Возвращает первый день месяца
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function getFirstDateOfMonth($date = null)
    {
        return static::getDate(static::mktime(self::getDate($date), null, null, null, null, 1));
    }

    /**
     * Возвращает последний день месяца
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function getLastDateOfMonth($date = null)
    {
        $date = self::getDate($date);
        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        return static::getDate(static::mktime($date, null, null, null, null, $class::getCountDaysInMonth($date->timestamp)));
    }

    /**
     * Возвращает дату, соответствующую первому дню года
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function getFirstDateOfYear($date = null)
    {
        return static::getDate(static::mktime(self::getDate($date), null, null, null, 1, 1));
    }

    /**
     * Возвращает дату, соответствующую последнему дню года
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @return \gear\models\GDate
     */
    public static function getLastDateOfYear($date = null)
    {
//        $class = self::getLocaleNamespace() . '\\' . self::getLocale();
        return static::factory(static::mktime(self::getDate($date), null, null, null, 12, 31));
    }

    /**
     * Возвращает массив дат соответствующих дням недели
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date 
     * @return array of \gear\models\GDate
     */
    public static function getDatesOfWeek($date = null)
    {
        $date = static::getFirstDateOfWeek($date);
        $dates = array($date);
        $timestamp = $date->timestamp;
        for ($day = 2; $day <= self::DAYS_PER_WEEK; ++ $day)
            $dates[] = static::getDate($timestamp + $day * self::SECONDS_PER_DAY);
        return $dates;
    }

    /**
     * Возвращает массив дат соответствующих месяцу
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date 
     * @return array of \gear\models\GDate
     */
    public static function getDatesOfMonth($date = null)
    {
        $date = static::getFirstDateOfMonth($date);
        $dates = array($date);
        $timestamp = $date->timestamp;
        $countDays = static::getCountDaysInMonth($date);
        for ($day = 1; $day < $countDays; ++$day)
            $dates[] = static::factory(['timestamp' => $timestamp + $day * self::SECONDS_PER_DAY]);
        return $dates;
    }

    /**
     * Возвращает массив дат соответствующих году
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date 
     * @return array of \gear\models\GDate
     */
    public static function getDatesOfYear($date = null)
    {
        $date = static::getFirstDateOfYear($date);
        $dates = array($date);
        $timestamp = $date->timestamp;
        $countDays = static::getCountDaysInYear($date);
        for ($day = 1; $day < $countDays; ++$day)
            $dates[] = static::factory(['timestamp' => $timestamp + $day * self::SECONDS_PER_DAY]);
        return $dates;
    }

    /**
     * Возвращает массив дат, ограниченный начальной датой $from и конечной $to,
     * включая эти даты
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $from
     * @param null|integer|string|\gear\models\GDate $to
     * @return array of \gear\models\GDate
     */
    public static function getRange($from, $to, $step = '1 day', $revert = false)
    {
        if (!is_object($from)) $from = static::getDate($from);
        if (!is_object($to)) $to = static::getDate($to);
        $ranger = function ($from, $to, $step, $revert) {
            $dates = array($from);
            $last = clone $from;
            $operation = !$revert ? 'add' : 'sub';
            while (true) {
                //echo $last . "\n";
                foreach ($step as $name => $value) {
                    $last->{$operation . ucfirst($name)}($value);
                }
                if ((!$revert && $last->timestamp >= $to->timestamp) || ($revert && $last->timestamp <= $to->timestamp))
                    break;
                $dates[] = $last;
                $last = clone $last;
            }
            $dates[] = $to;
            return $dates;
        };
        $step = static::_prepareStep($step);
        if ($from->timestamp === $to->timestamp)
            $dates = [$from, $to];
        else if ($from->timestamp < $to->timestamp)
            $dates = !$revert ? $ranger($from, $to, $step, false) : $ranger($to, $from, $step, true);
        else
            $dates = !$revert ? $ranger($from, $to, $step, true) : $ranger($to, $from, $step, false);
        return $dates;
    }

    protected function _prepareStep($step)
    {
        preg_match_all('/((\d+)\s*(\w+))/', $step, $founds);
//        $incs = array('years' => 0, 'months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0);
        $incs = array();
        if ($founds[3]) {
            foreach ($founds[3] as $i => $name) {
                if (in_array($name, ['year', 'years']))
                    $name = 'years';
                else if (in_array($name, ['mon', 'mons', 'month', 'months']))
                    $name = 'months';
                else if (in_array($name, ['week', 'weeks']))
                    $name = 'weeks';
                else if (in_array($name, ['day', 'days']))
                    $name = 'days';
                else if (in_array($name, ['hour', 'hours']))
                    $name = 'hours';
                else if (in_array($name, ['min', 'mins', 'minute', 'minutes']))
                    $name = 'minutes';
                else if (in_array($name, ['sec', 'secs', 'second', 'seconds']))
                    $name = 'hours';
                else
                    continue;
                $incs[$name] = $founds[2][$i];
            }
        }
        return $incs;
    }

    /**
     * Вычисление разницы между датами
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $dateOne
     * @param null|integer|string|\gear\models\GDate $dateTwo
     * @return string
     */
    public static function diff($dateOne, $dateTwo)
    {
        if (!is_object($dateOne))
            $dateOne = static::getDate($dateOne);
        if (!is_object($dateTwo))
            $dateTwo = static::getDate($dateTwo);
        $less = $more = $dateOne;
        $dateOne->timestamp < $dateTwo->timestamp ? $more = $dateTwo : $less = $dateTwo;
        $diff = [$more->year - $less->year, 0, 0, 0, 0, 0];
        $subs = function ($index, &$diff) use (&$subs) {
            if ($diff[$index])
                --$diff[$index];
            else
                if ($index)
                    $subs($index - 1, $diff);
        };
        $set = function ($index, &$diff, $value, $sub = false) use (&$subs) {
            $diff[$index] = $value;
            if ($sub)
                $subs($index - 1, $diff);
        };
        $less->month > $more->month ? $set(1, $diff, 12 - $less->month + $more->month, true) : $set(1, $diff, $more->month - $less->month, false);
        $less->day > $more->day ? $set(2, $diff, $less->getCountDaysInMonth() - $less->day + $more->day, true) : $set(2, $diff, $more->day - $less->day, false);
        $less->hour > $more->hour ? $set(3, $diff, 24 - $less->hour + $more->hour, true) : $set(3, $diff, $more->hour - $less->hour, false);
        $less->minute > $more->minute ? $set(4, $diff, 60 - $less->minute + $more->minute, true) : $set(4, $diff, $more->minute - $less->minute, false);
        $less->second > $more->second ? $set(5, $diff, 60 - $less->second + $more->second, true) : $set(5, $diff, $more->second - $less->second, false);
        return $diff;
    }

    /**
     * Возвращает разницу между указанной датой и текущей в
     * "человекопонятном стиле"
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $dateOne
     * @param null|integer|string|\gear\models\GDate $dateTwo
     * @return string
     */
    public static function humanDiff($dateOne, $dateTwo = null)
    {
        $dateOne = static::getDate($dateOne);
        $dateTwo = static::getDate($dateTwo);
        $class = static::getLocaleNamespace() . '\\' . static::getLocale();
        $mul = $dateOne->timestamp < $dateTwo->timestamp ? -1 : 1;
        $diff = static::diff($dateOne, $dateTwo);
        $result = '';
        if ($diff[0] > 0)
            return $class::getHumanDecline($diff[0] * $mul, 'diff', 'y');
        else if ($diff[1] > 0)
            return $class::getHumanDecline($diff[1] * $mul, 'diff', 'm');
        else if ($diff[2] > 0) {
            if ($diff[2] < 7)
                return $class::getHumanDecline($diff[2] * $mul, 'diff', 'd');
            else
                return $class::getHumanDecline((int)($diff[2] / 7) * $mul, 'diff', 'w');
        } else if ($diff[3] > 0)
            return $class::getHumanDecline($diff[3] * $mul, 'diff', 'h');
        else if ($diff[4] > 0)
            return $class::getHumanDecline($diff[4] * $mul, 'diff', 'i');
        else if ($diff[5] > 0) {
            if (($diff[5] > 50 && $diff[5] <= 59) || ($diff[5] < -50 && $diff[5] >= -59))
                return $class::getHumanDecline(1 * $mul, 'diff', 'i');
            else
                return $class::getHumanDecline($diff[5] * $mul, 'diff', 's');
        }
    }

    /**
     * Установка пространства имён локалей
     *
     * @access public
     * @param string $localeNamespace
     * @return $this
     */
    public static function setLocaleNamespace($localeNamespace)
    {
        static::$_localeNamespace = $localeNamespace;
    }

    /**
     * Возвращает namespace локалей
     *
     * @access public
     * @return string
     */
    public static function getLocaleNamespace()
    {
        return static::$_localeNamespace;
    }

    /**
     * Установка текушей локали
     *
     * @access public
     * @param string $locale
     * @return void
     */
    public static function setLocale($locale)
    {
        static::$_locale = $locale;
    }

    /**
     * возвращает текущую локаль
     *
     * @access public
     * @return string
     */
    public static function getLocale()
    {
        return static::$_locale;
    }

    /**
     * Установка формата вывода даты
     *
     * @access public
     * @param string $format
     * @return void
     */
    public static function setFormat($format)
    {
        static::$_factory['format'] = static::current()->format = static::$_format = $format;
    }

    /**
     * Возвращает формат вывода даты
     *
     * @access public
     * @return string
     */
    public static function getFormat()
    {
        return static::$_format;
    }

    /**
     * Установка или снятие флага использования падежного окончания, при
     * форматированном выводе полного названия месяца
     *
     * @access public
     * @param boolean $natural
     * @return void
     */
    public static function setNatural($natural)
    {
        static::$_factory['natural'] = static::current()->format = static::$_natural = (boolean)$natural;
    }

    /**
     * Получение значения флага использования падежного окончания, при
     * форматированном выводе полного названия месяца
     *
     * @access public
     * @return boolean
     */
    public static function getNatural()
    {
        return static::$_natural;
    }

    /**
     * Возвращает номер квартала
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date 
     * @return integer
     */
    public static function getQuarter($date = null)
    {
        return ceil(self::getDate($date)->month / 3);
    }

    /**
     * Возвращает true если дата соответствует високосному году, иначе false
     *
     * @access public
     * @param integer|string|object $date
     * @return boolean
     */
    public static function isLeap($date = null)
    {
        return (bool)date('L', static::getDate($date)->timestamp);
    }

    /**
     * Возвращает true, если указанный год високосный, иначе false
     *
     * @access public
     * @param string $year
     * @return boolean
     */
    public static function isLeapYear($year)
    {
        return (bool)$year % 4;
    }

    /**
     * Сравнивает две даты. Метод возвращает разницу в секундах
     * 0 - если даты равны
     * отрицательное значение (количество секунд) если date1 меньше date2
     * положительное значение (количество секунд) если date1 больше date2
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $dateOne
     * @param null|integer|string|\gear\models\GDate $dateTwo
     * @param integer $mode
     * @return integer
     */
    public static function compare($dateOne, $dateTwo, $mode = 1)
    {
        $dateOne = static::getDate($dateOne);
        $dateTwo = static::getDate($dateTwo);
        switch ($mode) {
            case 2 :
                return static::compareDate($dateOne, $dateTwo);
            case 3 :
                return static::compareTime($dateOne, $dateTwo);
            case 1 :
            default :
                return $dateOne->timestamp - $dateTwo->timestamp;
        }
    }

    /**
     * Сравнение только по дате без учёта времени
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $dateOne
     * @param null|integer|string|\gear\models\GDate $dateTwo
     * @return integer
     */
    public static function compareDate($dateOne, $dateTwo)
    {
        $dateOne = static::getDate($dateOne);
        $dateTwo = static::getDate($dateTwo);
        return strtotime($dateOne->format('Y-m-d')) - strtotime($dateTwo->format('Y-m-d'));
    }

    /**
     * Сравнение только по времени
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $dateOne
     * @param null|integer|string|\gear\models\GDate $dateTwo
     * @return integer
     */
    public static function compareTime($dateOne, $dateTwo)
    {
        $dateOne = static::getDate($dateOne);
        $dateTwo = static::getDate($dateTwo);
        $seconds1 = $dateOne->hour * self::SECONDS_PER_HOUR + $dateOne->minute * self::SECONDS_PER_MINUTE + $dateOne->second;
        $seconds2 = $dateTwo->hour * self::SECONDS_PER_HOUR + $dateTwo->minute * self::SECONDS_PER_MINUTE + $dateTwo->second;
        return $seconds1 - $seconds2;
    }

    /**
     * Формирует временную метку UNIX по указанным значениям
     *
     * @access public
     * @param null|integer|string|\gear\models\GDate $date
     * @param null|integer $hour
     * @param null|integer $minute
     * @param null|integer $second
     * @param null|integer $month
     * @param null|integer $day
     * @param null|integer $year
     * @return integer
     */
    public static function mktime($date, $hour = null, $minute = null, $second = null, $month = null, $day = null, $year = null)
    {
        $date = static::getDate($date);
        $timestamp = mktime(
            $hour ?: date('H', $date->timestamp),
            $minute ?: date('i', $date->timestamp),
            $second ?: date('s', $date->timestamp),
            $month ?: date('m', $date->timestamp),
            $day ?: date('d', $date->timestamp),
            $year ?: date('Y', $date->timestamp)
        );
        return $timestamp;
    }
}
