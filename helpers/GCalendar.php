<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GObject;
use gear\interfaces\IFactory;

define('MONTH_NUMBER', 1);
define('MONTH_FULLNAME', 2);
define('MONTH_SHORTNAME', 3);

/**
 * Класс для работы с календарём
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.06.2014
 * @php 5.3.x
 */
class GCalendar extends GObject implements IFactory
{
    /* Const */
    const SECONDS_PER_MINUTE = 60;
    const SECONDS_PER_HOUR = 3600;
    const SECONDS_PER_DAY = 86400;
    const DAYS_PER_WEEK = 7;
    /* Private */
    /* Protected */
    protected $_factory = array
    (
        'class' => '\gear\models\GDate',
        'format' => 'Y-m-d H:i:s',
        'natural' => false,
    );
    protected $_current = null;
    protected $_locale = 'ru_RU';
    protected $_localeNamespace = '\gear\helpers\locales';
    protected $_format = 'Y-m-d H:i:s';
    protected $_natural = false;
    /* Public */

    /**
     * Возвращает объект даты, указанной в качестве названия метода.
     * Пример
     * GCalendar::{'2015-06-29'}();
     *
     * @access public
     * @static
     * @param string $name
     * @param array $args
     * @return object
     * @php => 5.4
     */
    public static function __callStatic($name, $args) { return static::it()->getDate($name); }
    
    /**
     * Возвращает в отформатированном виде текущую (установленную) дату календаря
     * 
     * @access public
     * @return string
     */
    public function __toString()
    {
        if (!$this->_current)
            $this->_current = $this->now();
        return $this->_current->__toString();
    }

    /**
     * Создаёт экхемпляр календаря
     *
     * @access public
     * @param array $properties
     * @return GCalendar
     */
    public static function it(array $properties = array()) { return new static($properties); }

    /**
     * Создание экземпляра
     *
     * @access public
     * @param array $properties
     * @return object
     */
    public function factory($properties = array())
    {
        list($class, $config, $defaultProperties) = Core::getRecords($this->_factory);
        /** @var string $class */
        return new $class(array_merge($defaultProperties, $properties), $this);
    }
    
    /**
     * Текущая дата И время
     * 
     * @access public
     * @return object
     */
    public function now() { return $this->factory(array('timestamp' => time())); }
    
    /**
     * Возвращает затрашнюю дату
     * 
     * @access public
     * @return object
     */
    public function tomorrow() { return $this->factory(array('timestamp' => time() + self::SECONDS_PER_DAY)); }
    
    /**
     * Возвращает вчеращнюю дату
     * 
     * @access public
     * @return object
     */
    public function yesterday() { return $this->factory(array('timestamp' => time() - self::SECONDS_PER_DAY)); }

    /**
     * Следующий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextDay($date = null) { return $this->addDay($date); }
    
    /**
     * Предыдущий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousDay($date = null) { return $this->subDay($date); }
    
    /**
     * Следующий месяц
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextMonth($date = null) { return $this->addMonth($date); }
    
    /**
     * Предыдущий месяц
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousMonth($date = null) { return $this->subMonth($date); }
    
    /**
     * Следующий год
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextYear($date = null) { return $this->addYear($date); }
    
    /**
     * Предыдущий год
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousYear($date = null) { return $this->subYear($date); }
    
    /**
     * Следующий час
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextHour($date = null) { return $this->addHour($date); }
    
    /**
     * Предыдущий час
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousHour($date = null) { return $this->subHour($date); }
    
    /**
     * Следующая минута
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextMinute($date = null) { return $this->addMinute($date); }
    
    /**
     * Предыдущая минута
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousMinute($date = null) { return $this->subMinute($date); }
    
    /**
     * Следующая секунда
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextSecond($date = null) { return $this->addSecond($date); }
    
    /**
     * Предыдущая секунда
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousSecond($date = null) { return $this->subSecond($date); }
    
    /**
     * Следующая неделя
     * 
     * @access public
     * @param null|integer|string|object $date
     * @return object
     */
    public function nextWeek($date = null) { return $this->addDays($date, 7); }

    /**
     * Предыдущая неделя
     * 
     * @access public
     * @param null|integer|string|object $date
     * @return object
     */
    public function previousWeek($date = null) { return $this->subDays($date, 7); }

    /**
     * Установка даты в календаре
     *
     * @access public
     * @param object $date
     * @return void
     */
    public function setCurrent($date) { $this->_current = $date; }

    /**
     * Возвращает установленную дату в календаре (по-умолчанию текущая дата)
     *
     * @access public
     * @return object
     */
    public function getCurrent() { return $this->_current ?: $this->_current = $this->now(); }

    /**
     * Установка текущей даты календаря
     *
     * Значение $date может принимать значения:
     * - временную метку UNIX
     * - запись даты в формате, который понимает php-функция strtotime()
     * - объект класса, указанного в свойстве _factoryItem
     *
     * @access public
     * @param integer|string|object $date
     * @return object $object
     */
    public function setDate($date)
    {
        if (!is_object($date))
            $date = $this->factory(array('timestamp' => !is_numeric($date) ? strtotime($date) : $date));
        return $this->current = $date;
    }
    
    /**
     * Возвращает текущую дату календаря или создаёт новую по указанному
     * значению в параметре $date
     * 
     * @access public
     * @param null|integer|string $date
     * @return object
     */
    public function getDate($date = null) 
    {
        if ($date)
            return $this->factory(array('timestamp' => !is_numeric($date) ? strtotime($date) : $date));
        else
            return $this->current;
    }

    /**
     * Устанвока UNIX-timestamp
     *
     * @access public
     * @param integer $timestamp
     * @param null|object $date
     * @return object
     */
    public function setTimestamp($timestamp, $date = null)
    {
        return $date ? $date->setTimestamp($timestamp) : $this->current->setTimestamp($timestamp);
    }

    /**
     * Возвращает UNIX-timestamp
     *
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getTimestamp($date = null) { return $date ? $date->timestamp : $this->current->timestamp; }

    /**
     * Установка числа
     *
     * @access public
     * @param null|object $date
     * @param integer $day
     * @return object
     */
    public function setDay($date = null, $day)
    {
        return $this->setTimestamp($this->mktime($date, null, null, null, null, $day), $date);
    }

    /**
     * Возвращает число
     *
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getDay($date = null) { return date('d', $this->getTimestamp($date)); }

    /**
     * Добавление к текущей дате указанное число дней и возвращает новую
     * дату
     *
     * @access public
     * @param null|object $date
     * @param integer $days кол-во дней, которые необходимо прибавить
     * @return object
     */
    public function addDay($date = null) { return $this->addDays($date, 1); }
    public function addDays($date = null, $days)
    {
        return $this->setTimestamp($this->getTimestamp($date)  + $days * self::SECONDS_PER_DAY, $date);
    }

    /**
     * Вычитает из текущей даты указанное число дней и возвращает новую
     * дату
     *
     * @access public
     * @param null|object $date
     * @param integer $days кол-во дней, которые необходимо вычесть
     * @return object
     */
    public function subDay($date = null) { return $this->subDays($date, 1); }
    public function subDays($date = null, $days)
    {
        return $this->setTimestamp($this->getTimestamp($date)  - $days * self::SECONDS_PER_DAY, $date);
    }

    /**
     * Установка месяца
     *
     * @access public
     * @param null|object $date
     * @param integer $month
     * @return object
     */
    public function setMonth($date = null, $month)
    {
        return $this->setTimestamp($this->mktime($date, null, null, null, $month), $date);
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
     * @param null|object $date
     * @param integer $mode
     * @return integer
     */
    public function getMonth($date = null, $mode = MONTH_NUMBER)
    { 
        $class = $this->localeNamespace . '\\' . $this->locale;
        switch($mode)
        {
            case MONTH_SHORTNAME : return $class::getShortMonth($this->getTimestamp($date));
            case MONTH_FULLNAME : return $class::getFullMonth($this->getTimestamp($date));
            case MONTH_NUMBER :
            default : return date('m', $this->getTimestamp($date));
        } 
    }

    /**
     * Прибавляет к дате указанное количество месяцев
     * 
     * @access public
     * @param null|object $date
     * @param integer $months
     * @return object
     */
    public function addMonth($date = null) { return $this->addMonths($date, 1); }
    public function addMonths($date = null, $months)
    {
        return $this->setTimestamp(strtotime('+' . (int)$months . ' month', $this->getTimestamp($date)), $date);
    }

    /**
     * Вычитает из даты указанное количество месяцев
     * 
     * @access public
     * @param null|object $date
     * @param integer $months
     * @return object
     */
    public function subMonth($date = null) { return $this->subMonths($date, 1); }
    public function subMonths($date = null, $months)
    {
        return $this->setTimestamp(strtotime('-' . (int)$months . ' month', $this->getTimestamp($date)), $date);
    }

    /**
     * Установка года
     * 
     * @access public
     * @param null|object $date
     * @param integer $year
     * @return object
     */
    public function setYear($date = null, $year)
    {
        return $this->setTimestamp($this->mktime($date, null, null, null, null, null, $year), $date);
    }

    /**
     * Возвращает год
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getYear($date = null) { return date('Y', $this->getTimestamp($date)); }

    /**
     * Прибавляет к дате указанное количество лет
     * 
     * @access public
     * @param null|object $date
     * @param integer $years
     * @return object
     */
    public function addYear($date = null) { return $this->addYears($date, 1); }
    public function addYears($date = null, $years)
    {
        return $this->setYear($date, ($date ? $date->year : $this->current->year) + $years);
    }

    /**
     * Вычитает из даты указанное количество лет
     * 
     * @access public
     * @param null|object $date
     * @param integer $years
     * @return object
     */
    public function subYear($date = null) { return $this->subYears($date, 1); }
    public function subYears($date = null, $years)
    {
        return $this->setYear($date, ($date ? $date->year : $this->current->year) - $years);
    }

    /**
     * Установка часа
     * 
     * @access public
     * @param null|object $date
     * @param integer $hour
     * @return object
     */
    public function setHour($date = null, $hour) { return $this->setTimestamp($this->mktime($date, $hour), $date); }

    /**
     * Возвращает час
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getHour($date = null) { return date('H', $this->getTimestamp($date)); }

    /**
     * Прибавляет к времени указанное количество часов
     * 
     * @access public
     * @param null|object $date
     * @param integer $hours
     * @return object
     */
    public function addHour($date = null) { return $this->addHours($date, 1); }
    public function addHours($date = null, $hours)
    {
        return $this->setTimestamp($this->getTimestamp($date) + $hours * self::SECONDS_PER_HOUR);
    }

    /**
     * Вычитает из времени указанное количество часов
     * 
     * @access public
     * @param null|object $date
     * @param integer $hours
     * @return object
     */
    public function subHour($date = null) { return $this->subHours($date, 1); }
    public function subHours($date = null, $hours)
    {
        return $this->setTimestamp($this->getTimestamp($date) - $hours * self::SECONDS_PER_HOUR);
    }

    /**
     * Установка минут
     * 
     * @access public
     * @param null|object $date
     * @param integer $minute
     * @return object
     */
    public function setMinute($date = null, $minute)
    {
        return $this->setTimestamp($this->mktime($date, null, $minute), $date);
    }

    /**
     * Возвращает минуты
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getMinute($date = null) { return date('i', $this->getTimestamp($date)); }

    /**
     * Прибавляет к времени указанное количество минут
     * 
     * @access public
     * @param null|object $date
     * @param integer $minutes
     * @return object
     */
    public function addMinute($date = null) { return $this->addMinutes($date, 1); }
    public function addMinutes($date = null, $minutes)
    {
        return $this->setTimestamp($this->getTimestamp($date) + $minutes * self::SECONDS_PER_MINUTE);
    }

    /**
     * Вычитает из времени указанное количество минут
     * 
     * @access public
     * @param null|object $date
     * @param integer $minutes
     * @return object
     */
    public function subMinute($date = null) { return $this->subMinutes($date, 1); }
    public function subMinutes($date = null, $minutes)
    {
        return $this->setTimestamp($this->getTimestamp($date) - $minutes * self::SECONDS_PER_MINUTE);
    }

    /**
     * Установка секунд
     * 
     * @access public
     * @param null|object $date
     * @param integer $minute
     * @return object
     */
    public function setSecond($date = null, $second)
    {
        return $this->setTimestamp($this->mktime($date, null, null, $second), $date);
    }

    /**
     * Возвращает секунды
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getSecond($date = null) { return date('s', $this->getTimestamp($date)); }

    /**
     * Прибавляет к времени указанное количество секунд
     * 
     * @access public
     * @param null|object $date
     * @param integer $seconds
     * @return object
     */
    public function addSecond($date = null) { return $this->addSeconds($date, 1); }
    public function addSeconds($date = null, $seconds)
    {
        return $this->setTimestamp($this->getTimestamp($date) + $seconds);
    }

    /**
     * Вычитает из времени указанное количество секунд
     * 
     * @access public
     * @param null|object $date
     * @param integer $seconds
     * @return object
     */
    public function subSecond($date = null) { return $this->subSeconds($date, 1); }
    public function subSeconds($date = null, $seconds)
    {
        return $this->setTimestamp($this->getTimestamp($date) - $seconds);
    }

    /**
     * Прибавляет к дате указанное количество недель
     * 
     * @access public
     * @param null|object $date
     * @param integer $weeks
     * @return object
     */
    public function addWeek($date = null) { return $this->addWeeks($date, 1); }
    public function addWeeks($date = null, $weeks)
    {
        return $this->setTimestamp($this->getTimestamp() + $weeks * self::DAYS_PER_WEEK * self::SECONDS_PER_DAY, $date);
    }
    
    /**
     * Вычитает из даты указанное количество недель
     * 
     * @access public
     * @param null|object $date
     * @param integer $weeks
     * @return object
     */
    public function subWeek($date = null) { return $this->subWeeks($date, 1); }
    public function subWeeks($date = null, $weeks)
    {
        return $this->setTimestamp($this->getTimestamp() - $weeks * self::DAYS_PER_WEEK * self::SECONDS_PER_DAY, $date);
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
     * @param integer $mode
     * @return integer
     */
    public function getDayOfWeek($date = null, $mode = 1)
    {
        $class = $this->localeNamespace . '\\' . $this->locale;
        switch($mode)
        {
            case 2 : return $class::getFullWeek($this->getTimestamp($date));
            case 3 : return $class::getShortWeek($this->getTimestamp($date));
            case 1 :
            default : return $class::getNumberDayOfWeek($this->getTimestamp($date));
        }
    }
    
    /**
     * Возвращает номер дня в году
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getNumberOfDay($date = null) { return date('z', $this->getTimestamp($date)); }
    
    /**
     * Возвращает количество дней в месяце
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getCountDaysInMonth($date = null) { return date('t', $this->getTimestamp($date)); }
    
    /**
     * Возвращает количество дней в году
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getCountDaysInYear($date = null) { return date('L', $this->getTimestamp($date)) ? 366 : 365; }
    
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
     * @param null|object $date
     * @param integer $mode
     * @return array
     */
    public function getDaysOfWeek($date = null, $mode = 1)
    {
        $class = $this->localeNamespace . '\\' . $this->locale;
        switch($mode)
        {
            case 2 : return $class::getFullWeeks();
            case 3 : return $class::getShortWeeks();
            case 1 :
            default : return $class::getNumbersDayOfWeek();
        }
    }
    
    /**
     * Возвращает дату, которая соответствует первому дню недели
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getFirstDateOfWeek($date = null)
    {
        $class = $this->localeNamespace . '\\' . $this->locale;
        $timestamp = $this->getTimestamp($date);
        $firstDayOfWeek = (int)$class::getFirstNumberDayOfWeek();
        $dayOfWeek = (int)$class::getNumberDayOfWeek($timestamp);
        if ($firstDayOfWeek >= $dayOfWeek)
            return $this->factory(array('timestamp' => $timestamp));
        else
            return $this->factory(array('timestamp' => $timestamp - ($dayOfWeek - $firstDayOfWeek) * self::SECONDS_PER_DAY));
    }
    
    /**
     * Возвращает дату, которая соответствует последнему дню недели
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getLastDateOfWeek($date = null)
    {
        $class = $this->localeNamespace . '\\' . $this->locale;
        $timestamp = $this->getTimestamp($date);
        $lastDayOfWeek = (int)$class::getLastNumberDayOfWeek();
        $dayOfWeek = (int)$class::getNumberDayOfWeek($timestamp);
        if ($lastDayOfWeek <= $dayOfWeek)
            return $this->factory(array('timestamp' => $timestamp));
        else
            return $this->factory(array('timestamp' => $timestamp + ($lastDayOfWeek - $dayOfWeek) * self::SECONDS_PER_DAY));
    }
    
    /**
     * Возвращает первый день месяца
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getFirstDateOfMonth($date = null)
    {
        return $this->factory(array('timestamp' => $this->mktime($date, null, null, null, null, 1)));
    }
    
    /**
     * Возвращает последний день месяца
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getLastDateOfMonth($date = null)
    {
        return $this->factory(array('timestamp' => $this->mktime($date, null, null, null, null, date('t', $this->getTimestamp($date)))));
    }
    
    /**
     * Возвращает дату, соответствующую первому дню года
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getFirstDateOfYear($date = null)
    {
        return $this->factory(array('timestamp' => $this->mktime($date, null, null, null, 1, 1)));
    }
    
    /**
     * Возвращает дату, соответствующую последнему дню года
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getLastDateOfYear($date = null)
    {
//        $class = $this->localeNamespace . '\\' . $this->locale;
        return $this->factory(array('timestamp' => $this->mktime($date, null, null, null, 12, 31)));
    }
    
    /**
     * Возвращает массив дат соответствующих дням недели
     * 
     * @access public
     * @param null|object $date
     * @return array of objects
     */
    public function getDatesOfWeek($date = null)
    {
        $date = $this->getFirstDateOfWeek($date);
        $dates = array($date);
        $timestamp = $date->timestamp;
        for($day = 1; $day < self::DAYS_PER_WEEK; ++ $day)
            $dates[] = $this->factory(array('timestamp' => $timestamp + $day * self::SECONDS_PER_DAY));
        return $dates;
    }
    
    /**
     * Возвращает массив дат соответствующих месяцу
     * 
     * @access public
     * @param null|object $date
     * @return array of objects
     */
    public function getDatesOfMonth($date = null)
    {
        $date = $this->getFirstDateOfMonth($date);
        $dates = array($date);
        $timestamp = $date->timestamp;
        $countDays = $this->getCountDaysInMonth($date);
        for($day = 1; $day < $countDays; ++ $day)
            $dates[] = $this->factory(array('timestamp' => $timestamp + $day * self::SECONDS_PER_DAY));
        return $dates;
    }
    
    /**
     * Возвращает массив дат соответствующих году
     * 
     * @access public
     * @param null|object $date
     * @return array of objects
     */
    public function getDatesOfYear($date = null)
    {
        $date = $this->getFirstDateOfYear($date);
        $dates = array($date);
        $timestamp = $date->timestamp;
        $countDays = $this->getCountDaysInYear($date);
        for($day = 1; $day < $countDays; ++ $day)
            $dates[] = $this->factory(array('timestamp' => $timestamp + $day * self::SECONDS_PER_DAY));
        return $dates;
    }
    
    /**
     * Возвращает массив дат, ограниченный начальной датой $from и конечной $to,
     * включая эти даты
     * 
     * @access public
     * @param integer|string|object $from
     * @param integer|string|object $to
     * @return array of objects
     */
    public function getRangeDates($from, $to, $step = '1 day', $revert = false)
    {
        if (!is_object($from)) $from = $this->getDate($from);
        if (!is_object($to)) $to = $this->getDate($to);
        $operation = $from->timestamp <= $to->timestamp ? 'add' : 'sub';
        if ($step && preg_match('/^(\d+)\s(\w+)$/', $step, $founds))
            $method = [$this, $operation . ucfirst($founds[2]) . 's', $founds[1]];
        else
            $method = [$this, $operation . 'Days', 1];
        $stop = false;
        $dates = [$from];
        $date = $from;
        while(!$stop)
        {
            $date = call_user_func([$method[0], $method[1]], $date, $method[2]);
            if (($from->timestamp <= $to->timestamp && $date->timestamp < $to->timestamp) ||
                ($from->timestamp > $to->timestamp && $date->timestamp > $to->timestamp))
                $dates[] = $date;
            else
            {
                $dates[] = $to;
                $stop = true;
            }
        }
        if ($revert)
            krsort($dates);
        return $dates;
    }
    
    /**
     * Вычисление разницы между датами
     * 
     * @access public
     * @param integer|string|object $dateOne
     * @param integer|string|object $dateTwo
     * @return string
     */
    public function diff($dateOne, $dateTwo)
    {
        if (!is_object($dateOne))
            $dateOne = $this->getDate($dateOne);
        if (!is_object($dateTwo))
            $dateTwo = $this->getDate($dateTwo);
        $less = $more = $dateOne;
        $dateOne->timestamp < $dateTwo->timestamp ? $more = $dateTwo : $less = $dateTwo;
        $diff = [$more->year - $less->year, 0, 0, 0, 0, 0];
        $subs = function($index, &$diff) use (&$subs)
        {
            if ($diff[$index])
                -- $diff[$index];
            else
            if ($index)
                $subs($index - 1, $diff);
        };
        $set = function($index, &$diff, $value, $sub = false) use (&$subs)
        {
            $diff[$index] = $value;
            if ($sub)
                $subs($index - 1, $diff);
        };
        $less->month > $more->month ? $set(1, $diff, 12 - $less->month + $more->month, true) : $set(1, $diff, $more->month - $less->month, false);
        $less->day > $more->day ? $set(2, $diff, $less->getCountDaysInMonth() - $less->day + $more->day, true) : $set(2, $diff, $more->day - $less->day, false);
        $less->hour > $more->hour ? $set(3, $diff, 24 - $less->hour + $more->hour, true) : $set(3, $diff, $more->hour - $less->hour, false);
        $less->minute > $more->minute ? $set(4, $diff, 60 - $less->minute + $more->minute, true) : $set(4, $diff, $more->minute - $less->minute, false);
        $less->second > $more->second ? $set(5, $diff,60 - $less->second + $more->second, true) : $set(5, $diff, $more->second - $less->second, false);
        return $diff;
    }
    
    /**
     * Возвращает разницу между указанной датой и текущей в 
     * "человекопонятном стиле"
     * 
     * @access public
     * @return string
     */
    public function humanDiff($dateOne, $dateTwo = null)
    {
        $class = $this->getLocaleNamespace() . '\\' . $this->getLocale();
        if (!$dateTwo)
            $dateTwo = $this->getDate();
        $mul = $dateOne->timestamp < $dateTwo->timestamp ? -1 : 1;
        $diff = $this->diff($dateOne, $dateTwo);
        $result = '';
        if ($diff[0] > 0)
            return $class::getHumanDecline($diff[0] * $mul, 'diff', 'y');
        else
        if ($diff[1] > 0)
            return $class::getHumanDecline($diff[1] * $mul, 'diff', 'm');
        else
        if ($diff[2] > 0)
        {
            if ($diff[2] < 7)
                return $class::getHumanDecline($diff[2] * $mul, 'diff', 'd');
            else
                return $class::getHumanDecline((int)($diff[2] / 7) * $mul, 'diff', 'w');
        }
        else
        if ($diff[3] > 0)
            return $class::getHumanDecline($diff[3] * $mul, 'diff', 'h');
        else
        if ($diff[4] > 0)
            return $class::getHumanDecline($diff[4] * $mul, 'diff', 'i');
        else
        if ($diff[5] > 0)
        {
            if (($diff[5] > 50 && $diff[5] <= 59) || 
                ($diff[5] < -50 && $diff[5] >= -59))
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
    public function setLocaleNamespace($localeNamespace)
    {
        $this->_localeNamespace = $localeNamespace;
        return $this;
    }
    
    /**
     * Возвращает namespace локалей
     * 
     * @access public
     * @return string
     */
    public function getLocaleNamespace() { return $this->_localeNamespace; }
    
    /**
     * Установка текушей локали
     * 
     * @access public
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;
    }
    
    /**
     * возвращает текущую локаль
     * 
     * @access public
     * @return string
     */
    public function getLocale() { return $this->_locale; }

    /**
     * Установка формата вывода даты
     * 
     * @access public
     * @param string $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->_factory['format'] = $this->current->format = $this->_format = $format;
        return $this;
    }

    /**
     * Возвращает формат вывода даты
     * 
     * @access public
     * @return string
     */
    public function getFormat() { return $this->_format; }

    /**
     * Установка или снятие флага использования падежного окончания, при 
     * форматированном выводе полного названия месяца
     *
     * @access public 
     * @param mixed $natural
     * @return $this
     */
    public function setNatural($natural)
    {
        $this->_factory['natural'] = $this->curent->format = $this->_natural = (boolean)$natural;
        return $this;
    }

    /**
     * Получение значения флага использования падежного окончания, при 
     * форматированном выводе полного названия месяца
     * 
     * @access public
     * @return boolean
     */
    public function getNatural() { return $this->_natural; }
    
    /**
     * Возвращает номер квартала
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getQuarter($date = null)
    {
        return ceil($date ? $date->month : $this->_current->month / 3);
    }

    /**
     * Возвращает true если дата соответствует високосному году, иначе false
     *
     * @access public
     * @param integer|string|object $date
     * @return boolean
     */
    public function isLeap($date = null)
    {
        if (!$date)
            $date = $this->_current;
        else
        if (!is_object($date) && !is_numeric($date))
            $date = strtotime($date);
        return (bool)date('L', is_object($date) ? $date->timestamp : $date);
    }
    
    /**
     * Возвращает true, если указанный год високосный, иначе false
     * 
     * @access public
     * @param string $year
     * @return boolean
     */
    public function isLeapYear($year)
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
     * @param integer|string|object $date1
     * @param integer|string|object $date2
     * @param integer $mode
     * @return integer
     */
    public function compare($date1, $date2, $mode = 1)
    {
        if (!is_object($date1))
            $date1 = $this->getDate($date1);
        if (!is_object($date2))
            $date2 = $this->getDate($date2);
        switch($mode)
        {
            case 2 : return $this->compareDate($date1, $date2);
            case 3 : return $this->compareTime($date1, $date2);
            case 1 : 
            default : return $date1->timestamp - $date2->timestamp;
        }
    }
    
    /**
     * Сравнение только по дате без учёта времени
     * 
     * @access public
     * @param integer|string|object $date1
     * @param integer|string|object $date2
     * @return integer
     */
    public function compareDate($date1, $date2)
    {
        if (!is_object($date1))
            $date1 = $this->getDate($date1);
        if (!is_object($date2))
            $date2 = $this->getDate($date2);
        return strtotime($date1->format('Y-m-d')) - strtotime($date2->format('Y-m-d'));
    }

    /**
     * Сравнение только по времени
     * 
     * @access public
     * @param integer|string|object $date1
     * @param integer|string|object $date2
     * @return integer
     */
    public function compareTime($date1, $date2)
    {
        if (!is_object($date1))
            $date1 = $this->getDate($date1);
        if (!is_object($date2))
            $date2 = $this->getDate($date2);
        $seconds1 = $date1->hour * self::SECONDS_PER_HOUR + $date1->minute * self::SECONDS_PER_MINUTE + $date1->second;
        $seconds2 = $date2->hour * self::SECONDS_PER_HOUR + $date2->minute * self::SECONDS_PER_MINUTE + $date2->second;
        return $seconds1 - $seconds2; 
    }

    /**
     * Формирует временную метку UNIX по указанным значениям
     * 
     * @access public
     * @param null|object $date
     * @param null|integer $hour
     * @param null|integer $minute
     * @param null|integer $second
     * @param null|integer $month
     * @param null|integer $day
     * @param null|integer $year
     * @return integer
     */
    public function mktime($date, $hour = null, $minute = null, $second = null, $month = null, $day = null, $year = null)
    {
        $date = $date ?: $this->current;
        $timestamp = mktime
        (
            $hour !== null ? $hour : date('H', $date->timestamp),
            $minute !== null ? $minute : date('i', $date->timestamp),
            $second !== null ? $second : date('s', $date->timestamp),
            $month !== null ? $month : date('m', $date->timestamp),
            $day !== null ? $day : date('d', $date->timestamp),
            $year !== null ? $year : date('Y', $date->timestamp)
        );
        return $timestamp;
    }

    /**
     * Обработчик события onConstructed, заполняет текущую дату
     *
     * @access public
     * @return boolean
     */
    public function onConstructed()
    {
        parent::onConstructed();
        $this->current = $this->now();
        return true;
    }
}
