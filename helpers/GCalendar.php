<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GObject;
use gear\interfaces\IFactory;

/**
 * Класс для работы с календарём
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.06.2014
 */
class GCalendar extends GObject
{
    /* Const */
    const SECONDS_PER_MINUTE = 60;
    const SECONDS_PER_HOUR = 3600;
    const SECONDS_PER_DAY = 86400;
    /* Private */
    /* Protected */
    protected $_factoryItem = array
    (
        'class' => '\gear\helpers\GDate',
    );
    protected $_current = null;
    protected $_locale = 'ru_RU';
    protected $_localeNamespace = '\gear\helpers\locales';
    /* Public */
    
    /**
     * Возвращает в отформатированном виде текущую дату календаря
     * 
     * @access public
     * @return string
     */
    public function __toString() { return $this->_current->format(); }
    
    /**
     * Создание экземпляра
     * 
     * @access public
     * @param array $properties
     * @param string|array $class
     * @return object
     */
    public function factory(array $properties, $class = null)
    {
        list($class, $config, $defaultProperties) = Core::getRecords($this->_factoryItem);
        return new $class(array_merge($defaultProperties, $properties, array('owner' => $this)));
    }
    
    /**
     * Текущая дата И время
     * 
     * @access public
     * @return object
     */
    public function now()
    {
        return $this->_current = $this->factory(array('timestamp' => time()));
    }
    
    /**
     * Возвращает затрашнюю дату
     * 
     * @access public
     * @return object
     */
    public function tomorrow()
    {
        return $this->_current = $this->factory(array('timestamp' => strtotime('+1 day')));
    }
    
    /**
     * Возвращает вчеращнюю дату
     * 
     * @access public
     * @return object
     */
    public function yesterday()
    {
        return $this->_current = $this->factory(array('timestamp' => strtotime('-1 day')));
    }

    /**
     * Следующий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextDay(\gear\library\GObject $date = null)
    {
        $tm = strtotime('+1 day', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Предыдущий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousDay(\gear\library\GObject $date = null)
    {
        $tm = strtotime('-1 day', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Следующий месяц
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextMonth(\gear\library\GObject $date = null)
    {
        $tm = strtotime('+1 month', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Предыдущий месяц
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousMonth(\gear\library\GObject $date = null)
    {
        $tm = strtotime('-1 month', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Следующий год
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextYear(\gear\library\GObject $date = null)
    {
        $tm = strtotime('+1 year', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Предыдущий год
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousYear(\gear\library\GObject $date = null)
    {
        $tm = strtotime('-1 year', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Следующий час
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextHour(\gear\library\GObject $date = null)
    {
        $tm = strtotime('+1 hour', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Предыдущий час
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousHour(\gear\library\GObject $date = null)
    {
        $tm = strtotime('-1 hour', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Следующая минута
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextMinute(\gear\library\GObject $date = null)
    {
        $tm = strtotime('+1 minute', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Предыдущая минута
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousMinute(\gear\library\GObject $date = null)
    {
        $tm = strtotime('-1 minute', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Следующая секунда
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextSecond(\gear\library\GObject $date = null)
    {
        $tm = strtotime('+1 second', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }
    
    /**
     * Предыдущая секунда
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousSecond(\gear\library\GObject $date = null)
    {
        $tm = strtotime('-1 second', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $this->factory(array('timestamp' => $tm)) : $this->_current->setTimestamp($tm);
    }

    /**
     * Установка текущей даты календаря
     * 
     * Значение $date может принимать значения:
     * - временную метку UNIX
     * - запись даты в формате, который понимает php-функция strtotime()
     * - объект класса, указанного в свойстве _factoryItem
     * 
     * @access public
     * @param integer|string $date
     * @return $object
     */ 
    public function setDate($date)
    {
        if (!is_object($date) && !is_numeric($date))
            $date = strtotime($date);
        return $this->_current = is_object($date) ? $date : $this->factory(array('timestamp' => $date));
    }
    
    /**
     * Возвращает текущую дату календаря или создаёт новую по указанному
     * значению в параметре $date
     * 
     * @access public
     * @param null|integer|string $date
     * @return object
     */
    public function getDate($date) 
    {
        return $date ? $this->setDate($date) : $this->_current; 
    }

    /**
     * Установка числа
     *
     * @access public
     * @param null|object $date
     * @param integer $day
     * @return object
     */
    public function setDay($date = null, $day = 1)
    {
        $tm = $this->_createDate($date, null, null, null, null, $day);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Возвращает число
     *
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getDay($date = null) { return date('d', $date ? $date->timestamp : $this->_current->timestamp); }

    /**
     * Добавление к текущей дате указанное число дней и возвращает новую
     * дату
     *
     * @access public
     * @param null|object $date
     * @param integer $days кол-во дней, которые необходимо прибавить
     * @return obejct
     */
    public function addDays($date = null, $days = 0)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) + $days * self::SECONDS_PER_DAY;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
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
    public function subDays($date = null, $days)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) - $days * self::SECONDS_PER_DAY;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Установка месяца
     *
     * @access public
     * @param null|object $date
     * @param integer $month
     * @return object
     */
    public function setMonth($date = null, $month = 1)
    {
        $tm = $this->_createDate($date, null, null, null, $month);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
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
    public function getMonth($date = null, $mode = 1) 
    { 
        $class = $this->localeNamespace . '\\' . $this->locale;
        switch($mode)
        {
            case 2 : return $class::getFullMonth($date ? $date->timestamp : $this->_current->timestamp);
            case 3 : return $class::getShortMonth($date ? $date->timestamp : $this->_current->timestamp);
            case 1 : 
            default : return date('m', $date ? $date->timestamp : $this->_current->timestamp);
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
    public function addMonths($date = null, $months = 0)
    {
        $tm = strtotime('+' . (int)$months . ' month', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Вычитает из даты указанное количество месяцев
     * 
     * @access public
     * @param null|object $date
     * @param integer $months
     * @return object
     */
    public function subMonths($date = null, $months = 0)
    {
        $tm = strtotime('-' . (int)$months . ' month', $date ? $date->timestamp : $this->_current->timestamp);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Установка года
     * 
     * @access public
     * @param null|object $date
     * @param integer $year
     * @return object
     */
    public function setYear($date = null, $year = 0)
    {
        if (!$year)
            $year = date('Y');
        $tm = $this->_createDate($date, null, null, null, null, null, $year);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Возвращает год
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getYear($date = null) { return date('Y', $date ? $date->timestamp : $this->_current->timestamp); }

    /**
     * Прибавляет к дате указанное количество лет
     * 
     * @access public
     * @param null|object $date
     * @param integer $years
     * @return object
     */
    public function addYears($date = null, $years = 0)
    {
        $tm = $this->_createDate($date, null, null, null, null, null, $this->getYear($date ? $date : $this->_current) + $years);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Вычитает из даты указанное количество лет
     * 
     * @access public
     * @param null|object $date
     * @param integer $years
     * @return object
     */
    public function subYears($date = null, $years = 0)
    {
        $tm = $this->_createDate($date, null, null, null, null, null, $this->getYear($date ? $date : $this->_current) - $years);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Установка часа
     * 
     * @access public
     * @param null|object $date
     * @param integer $hour
     * @return object
     */
    public function setHour($date = null, $hour = 0)
    {
        $tm = $this->_createDate($date, $hour);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Возвращает час
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getHour($date = null) { return date('H', $date ? $date->timestamp : $this->_current->timestamp); }

    /**
     * Прибавляет к времени указанное количество часов
     * 
     * @access public
     * @param null|object $date
     * @param integer $hours
     * @return object
     */
    public function addHours($date = null, $hours = 0)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) + $hours * self::SECONDS_PER_HOUR;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Вычитает из времени указанное количество часов
     * 
     * @access public
     * @param null|object $date
     * @param integer $hours
     * @return object
     */
    public function subHours($date = null, $hours = 0)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) - $hours * self::SECONDS_PER_HOUR;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Установка минут
     * 
     * @access public
     * @param null|object $date
     * @param integer $minute
     * @return object
     */
    public function setMinute($date = null, $minute = 0)
    {
        $tm = $this->_createDate($date, null, $minute);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Возвращает минуты
     * 
     * @access public
     * @param null|object $date
     * @return object
     */
    public function getMinute($date = null) { return date('i', $date ? $date->timestamp : $this->_current->timestamp); }

    /**
     * Прибавляет к времени указанное количество минут
     * 
     * @access public
     * @param null|object $date
     * @param integer $minutes
     * @return object
     */
    public function addMinutes($date = null, $minutes = 0)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) + $minutes * self::SECONDS_PER_MINUTE;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Вычитает из времени указанное количество минут
     * 
     * @access public
     * @param null|object $date
     * @param integer $minutes
     * @return object
     */
    public function subMinutes($date = null, $minutes = 0)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) - $minutes * self::SECONDS_PER_MINUTE;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Установка секунд
     * 
     * @access public
     * @param null|object $date
     * @param integer $minute
     * @return object
     */
    public function setSecond($date = null, $second = 0)
    {
        $tm = $this->_createDate($date, null, null, $second);
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Возвращает секунды
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getSecond($date = null) { return date('s', $date ? $date->timestamp : $this->_current->timestamp); }

    /**
     * Прибавляет к времени указанное количество секунд
     * 
     * @access public
     * @param null|object $date
     * @param integer $seconds
     * @return object
     */
    public function addSeconds($date = null, $seconds = 0)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) + $seconds;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
    }

    /**
     * Вычитает из времени указанное количество секунд
     * 
     * @access public
     * @param null|object $date
     * @param integer $seconds
     * @return object
     */
    public function subSeconds($date = null, $seconds = 0)
    {
        $tm = ($date ? $date->timestamp : $this->_current->timestamp) - $seconds;
        return $date ? $date->setTimestamp($tm) : $this->_current->setTimestamp($tm);
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
            case 2 : return $class::getFullWeek($date ? $date->timestamp : $this->_current->timestamp);
            case 3 : return $class::getShortWeek($date ? $date->timestamp : $this->_current->timestamp);
            case 1 :
            default : return $class::getNumberDayOfWeek($date ? $date->timestamp : $this->_current->timestamp);
        }
    }
    
    /**
     * Возвращает номер дня в году
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getNumberOfDay($date = null)
    {
        return date('z', $date ? $date->timestamp : $this->_current->timestamp);
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
        $timestamp = $date ? $date->timestamp : $this->_current->timestamp;
        $firstDayOfweek = (int)$class::getFirstNumberDayOfWeek();
        $dayOfWeek = (int)$class::getNumberDayOfWeek($timestamp);
        if ($firstDayOfweek >= $dayOfWeek)
            return $date ? $date : $this->_current = $this->factory(array('timestamp' => $timestamp));
        else
        if ($firstDayOfweek < $dayOfWeek)
            return $this->subDays($date, $dayOfWeek - $firstDayOfweek);
            
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
        $timestamp = $date ? $date->timestamp : $this->_current->timestamp;
        $lastDayOfweek = (int)$class::getLastNumberDayOfWeek();
        $dayOfWeek = (int)$class::getNumberDayOfWeek($timestamp);
        if ($lastDayOfweek <= $dayOfWeek)
            return $date ? $date : $this->_current = $this->factory(array('timestamp' => $timestamp));
        else
        if ($lastDayOfweek > $dayOfWeek)
            return $this->addDays($date, $lastDayOfweek - $dayOfWeek);
            
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
        $class = $this->localeNamespace . '\\' . $this->locale;
        return $date ? $date->setDay(1) : $this->_current->setDay(1);
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
        $class = $this->localeNamespace . '\\' . $this->locale;
        return $date ? $date->setDay(date('t')) : $this->_current->setDay(date('t'));
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
        $class = $this->localeNamespace . '\\' . $this->locale;
        $date = $date ? $date->setDay($class::getFirstDayOfYear()) : $this->_current->setDay($class::getFirstDayOfYear());
        return $date->setMonth($class::getFirstMonthOfYear());
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
        $class = $this->localeNamespace . '\\' . $this->locale;
        $date = $date ? $date->setDay($class::getLastDayOfYear()) : $this->_current->setDay($class::getLastDayOfYear());
        return $date->setMonth($class::getLastMonthOfYear());
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
        for($day = $date->dayOfWeek + 1; $day <= 7; ++ $day)
            $dates[] = $date = $date->nextDay();
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
        $countDays = date('t', $date->timestamp);
        for($day = 2; $day <= $countDays; ++ $day)
        {
            $date = clone $date;
            $dates[] = $date->setDay($day);
        }
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
        $countDays = date('L', $date->timestamp) ? 366 : 365;
        for($day = 2; $day <= $countDays; ++ $day)
            $dates[] = $date = $date->nextDay();
        return $dates;
    }
    
    public function getRangeDates($from, $to)
    {
        $countDays = $to->getNumberOfDay() - $from->getNumberOfDay();
        $dates = array($from);
        $date = $from;
        for($day = 2; $day < $countDays; ++ $day)
            $dates[] = $date = $date->nextDay();
        $dates[] = $to;
        return $dates;
    }
    
    /**
     * Возвращает namespace локалей
     * 
     * @access public
     * @return string
     */
    public function getLocaleNamespace() { return $this->_localeNamespace; }
    
    /**
     * возвращает текущую локаль
     * 
     * @access public
     * @return string
     */
    public function getLocale() { return $this->_locale; }

    /**
     * Обработчик события onConstructed, заполняет текущую дату
     * 
     * @access public
     * @return boolean
     */
    public function onConstructed()
    {
        parent::onConstructed();
        $this->now();
        return true;
    }

    /**
     * Формирует временную метку UNIX по указанным значениям
     * 
     * @access private
     * @param null|object $date
     * @param null|integer $hour
     * @param null|integer $minute
     * @param null|integer $second
     * @param null|integer $month
     * @param null|integer $day
     * @param null|integer $year
     * @return integer
     */
    private function _createDate($date, $hour = null, $minute = null, $second = null, $month = null, $day = null, $year = null)
    {
        $timestamp = mktime
        (
            $hour ? $hour : date('G', $date ? $date->timestamp : $this->_current->timestamp), 
            $minute ? $minute : date('i', $date ? $date->timestamp : $this->_current->timestamp), 
            $second ? $second : date('s', $date ? $date->timestamp : $this->_current->timestamp), 
            $month ? $month : date('n', $date ? $date->timestamp : $this->_current->timestamp), 
            $day ? $day : date('j', $date ? $date->timestamp : $this->_current->timestamp), 
            $year ? $year : date('Y', $date ? $date->timestamp : $this->_current->timestamp)
        );
        return $timestamp;
    }
}
