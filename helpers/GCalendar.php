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
    protected $_format = 'Y-m-d H:i:s';
    protected $_natural = false;
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
        return $this->factory(array('timestamp' => time()));
    }
    
    /**
     * Возвращает затрашнюю дату
     * 
     * @access public
     * @return object
     */
    public function tomorrow()
    {
        return $this->factory(array('timestamp' => strtotime('+1 day')));
    }
    
    /**
     * Возвращает вчеращнюю дату
     * 
     * @access public
     * @return object
     */
    public function yesterday()
    {
        return $this->factory(array('timestamp' => strtotime('-1 day')));
    }

    /**
     * Следующий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextDay(\gear\library\GObject $date = null) { return $this->addDays($date, 1); }
    
    /**
     * Предыдущий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousDay(\gear\library\GObject $date = null) { return $this->subDays($date, 1); }
    
    /**
     * Следующий месяц
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextMonth(\gear\library\GObject $date = null) { return $this->addMonths($date, 1); }
    
    /**
     * Предыдущий месяц
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousMonth(\gear\library\GObject $date = null) { return $this->subMonths($date, 1); }
    
    /**
     * Следующий год
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextYear(\gear\library\GObject $date = null) { return $this->addYears($date, 1); }
    
    /**
     * Предыдущий год
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousYear(\gear\library\GObject $date = null) { return $this->subYears($date, 1); }
    
    /**
     * Следующий час
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextHour(\gear\library\GObject $date = null) { return $this->addHours($date, 1); }
    
    /**
     * Предыдущий час
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousHour(\gear\library\GObject $date = null) { return $this->subHours($date, 1); }
    
    /**
     * Следующая минута
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextMinute(\gear\library\GObject $date = null) { return $this->addMinutes($date, 1); }
    
    /**
     * Предыдущая минута
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousMinute(\gear\library\GObject $date = null) { return $this->subMinutes($date, 1); }
    
    /**
     * Следующая секунда
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function nextSecond(\gear\library\GObject $date = null) { return $this->addSeconds($date, 1); }
    
    /**
     * Предыдущая секунда
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previousSecond(\gear\library\GObject $date = null) { return $this->subSeconds($date, 1); }

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
        $timestamp = $this->_createDate($date, null, null, null, null, $day);
        return $date ? $date->setTimestamp($timestamp) : $this->_current->setTimestamp($timestamp);
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) + $days * self::SECONDS_PER_DAY));
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) - $days * self::SECONDS_PER_DAY));
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
        $timestamp = $this->_createDate($date, null, null, null, $month);
        return $date ? $date->setTimestamp($timestamp) : $this->_current->setTimestamp($timestamp);
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
        return $this->factory(array('timestamp' => strtotime('+' . (int)$months . ' month', $date ? $date->timestamp : $this->_current->timestamp)));
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
        return $this->factory(array('timestamp' => strtotime('-' . (int)$months . ' month', $date ? $date->timestamp : $this->_current->timestamp)));
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
        $timestamp = $this->_createDate($date, null, null, null, null, null, $year);
        return $date ? $date->setTimestamp($timestamp) : $this->_current->setTimestamp($timestamp);
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
        $timestamp = $date ? $date->timestamp : $this->_current->timestamp;
        return $this->factory(array('timestamp' => $timestamp + (date('L', $timestamp) ? 366 : 365) * self::SECONDS_PER_DAY));
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
        $timestamp = $date ? $date->timestamp : $this->_current->timestamp;
        return $this->factory(array('timestamp' => $timestamp - (date('L', $timestamp) ? 366 : 365) * self::SECONDS_PER_DAY));
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
        $timestamp = $this->_createDate($date, $hour);
        return $date ? $date->setTimestamp($timestamp) : $this->_current->setTimestamp($timestamp);
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) + $hours * self::SECONDS_PER_HOUR));
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) - $hours * self::SECONDS_PER_HOUR));
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
        $timestamp = $this->_createDate($date, null, $minute);
        return $date ? $date->setTimestamp($timestamp) : $this->_current->setTimestamp($timestamp);
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) + $minutes * self::SECONDS_PER_MINUTE));
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) - $minutes * self::SECONDS_PER_MINUTE));
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
        $timestamp = $this->_createDate($date, null, null, $second);
        return $date ? $date->setTimestamp($timestamp) : $this->_current->setTimestamp($timestamp);
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) + $seconds));
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
        return $this->factory(array('timestamp' => ($date ? $date->timestamp : $this->_current->timestamp) - $seconds));
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
     * Возвращает количество дней в месяце
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getCountDaysInMonth($date = null)
    {
        return date('t', $date ? $date->timestamp : $this->_current->timestamp);
    }
    
    /**
     * Возвращает количество дней в году
     * 
     * @access public
     * @param null|object $date
     * @return integer
     */
    public function getCountDaysInYear($date = null)
    {
        return date('L', $date ? $date->timestamp : $this->_current->timestamp) ? 366 : 365;
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
            return $date ? $date : $this->factory(array('timestamp' => $timestamp));
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
            return $date ? $date : $this->factory(array('timestamp' => $timestamp));
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
        return $this->factory(array('timestamp' => $this->_createDate($date ? $date : $this->_current, null, null, null, null, 1)));
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
        if (!$date)
            $date = $this->_current;
        return $this->factory(array('timestamp' => $this->_createDate($date, null, null, null, null, date('t', $date->timestamp))));
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
        return $this->factory(array
        (
            'timestamp' => $this->_createDate($date, null, null, null, $class::getFirstMonthOfYear(), $class::getFirstDayOfYear())
        ));
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
        return $this->factory(array
        (
            'timestamp' => $this->_createDate($date, null, null, null, $class::getLastMonthOfYear(), $class::getLastDayOfYear())
        ));
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
        $dates = array();
        foreach($this->getDaysOfWeek($date) as $day)
        {
            $dates[] = $date;
            $date = $date->addDays(1);
        }
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
        $countDays = $this->getCountDaysInMonth($date);
        for($day = 2; $day <= $countDays; ++ $day)
            $dates[] = $date = $date->addDays(1);
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
        $countDays = $this->getCountDaysInYear($date);
        for($day = 2; $day <= $countDays; ++ $day)
            $dates[] = $date = $date->addDays(1);
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
    public function getRangeDates($from, $to, $revert = false)
    {
        if (!is_object($from))
            $from = $this->getDate($from);
        if (!is_object($to))
            $to = $this->getDate($to);
        if ($from->timestamp > $to->timstamp)
        {
            $countDays =  $from->getNumberOfDay() - $to->getNumberOfDay();
            $dates = array($from);
            $date = $from;
            for($day = $countDays - 1; $day >= 2; -- $day)
                $dates[] = $date = $date->previousDay();
            $dates[] = $to;
        }
        else
        {
            $countDays = $to->getNumberOfDay() - $from->getNumberOfDay();
            $dates = array($from);
            $date = $from;
            for($day = 2; $day < $countDays; ++ $day)
                $dates[] = $date = $date->nextDay();
            $dates[] = $to;
        }
        if ($revert)
            krsort($dates);
        return $dates;
    }
    
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
        $this->_format = $format;
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
        $this->_natural = (boolean)$natural;
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
     * Обработчик события onConstructed, заполняет текущую дату
     * 
     * @access public
     * @return boolean
     */
    public function onConstructed()
    {
        parent::onConstructed();
        $this->date = $this->now();
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
            $hour !== null ? $hour : date('G', $date ? $date->timestamp : $this->_current->timestamp), 
            $minute !== null ? $minute : date('i', $date ? $date->timestamp : $this->_current->timestamp), 
            $second !== null ? $second : date('s', $date ? $date->timestamp : $this->_current->timestamp), 
            $month !== null ? $month : date('n', $date ? $date->timestamp : $this->_current->timestamp), 
            $day !== null ? $day : date('j', $date ? $date->timestamp : $this->_current->timestamp), 
            $year !== null ? $year : date('Y', $date ? $date->timestamp : $this->_current->timestamp)
        );
        return $timestamp;
    }
}
