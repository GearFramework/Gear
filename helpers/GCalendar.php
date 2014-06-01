<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GObject;
use gear\interfaces\IFactory;

class GCalendar extends GObject
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_factoryItem = array
    (
        'class' => '\gear\helpers\GDate',
    );
    protected $_current = null;
    protected $_day = 0;
    protected $_month = 0;
    protected $_year = 0;
    protected $_hour = 0;
    protected $_minute = 0;
    protected $_second = 0;
    protected $_locale = 'ru_RU';
    protected $_localeNamespace = '\gear\helpers\locales';
    /* Public */
    
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
        $date = new $class(array_merge($defaultProperties, $properties));
        return $date->owner = $this;
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
     * Следующий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function next(\gear\library\GObject $date = null)
    {
        return $this->_current = $this->factory(array('timestamp' => strtotime('+1 day', $date ? $date->timestamp : $this->now()->timstamp)));
    }
    
    /**
     * Предыдущий день, относительно указанного в параметре
     * 
     * @access public
     * @param object $date
     * @return object
     */
    public function previous(\gear\library\GObject $date = null)
    {
        return $this->_current = $this->factory(array('timestamp' => strtotime('-1 day', $date ? $date->timestamp : $this->now()->timstamp)));
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
     * Установка числа
     *
     * @access public
     * @param integer $day
     * @return $this
     */
    public function setDay($day)
    {
        $this->_day = $day;
        return $this->_changeDate();
    }

    /**
     * Возвращает число
     *
     * @access public
     * @return integer
     */
    public function getDay() { return $this->_day; }

    /**
     * Добавление к текущей дате указанное число дней и возвращает новую
     * дату
     *
     * @access public
     * @param integer $days кол-во дней, которые необходимо прибавить
     * @return $this
     */
    public function addDays($days)
    {
        $this->timestamp = $this->_timestamp + $days * self::SECONDS_PER_DAY;
        return $this;
    }

    /**
     * Вычитает из текущей даты указанное число дней и возвращает новую
     * дату
     *
     * @access public
     * @param integer $days кол-во дней, которые необходимо вычесть
     * @return $this
     */
    public function subDays($days)
    {
        $this->timestamp = $this->_timestamp - $days * self::SECONDS_PER_DAY;
        return $this;
    }

    /**
     * Установка месяца
     *
     * @access public
     * @param integer $month
     * @return $this
     */
    public function setMonth($month)
    {
        $this->_month = $month;
        return $this->_changeDate();
    }

    /**
     * Получение месяца
     *
     * @access public
     * @return integer
     */
    public function getMonth() { return $this->_month; }

    public function addMonths($months)
    {
        $time = strtotime('+' . (int)$months . ' month', $this->_timestamp);
        $this->timestamp = $time;
        return $this;
    }

    public function subMonths($months)
    {
        $time = strtotime('-' . (int)$months . ' month', $this->_timestamp);
        $this->timestamp = $time;
        return $this;
    }

    public function setYear($year)
    {
        $this->_year = $year;
        return $this->_changeDate();
    }

    public function getYear() { return $this->_year; }

    public function addYears($years)
    {
        $this->year = $this->year + $years;
        return $this;
    }

    public function subYears($years)
    {
        $this->year = $this->year - $years;
        return $this;
    }

    public function setHour($hour)
    {
        $this->_hour = $hour;
        return $this->_changeDate();
    }

    public function getHour() { return $this->_hour; }

    public function addHours($hours)
    {
        $this->timestamp = $this->_timestamp + $hours * self::SECONDS_PER_HOUR;
        return $this;
    }

    public function subHours($hours)
    {
        $this->timestamp = $this->_timestamp - $hours * self::SECONDS_PER_HOUR;
        return $this;
    }

    public function setMinute($minute)
    {
        $this->_minute = $minute;
        return $this->_changeDate();
    }

    public function getMinute() { return $this->_minute; }

    public function addMinutes($minutes)
    {
        $this->timestamp = $this->_timestamp + $minutes * self::SECONDS_PER_MINUTE;
        return $this;
    }

    public function subMinutes($minutes)
    {
        $this->timestamp = $this->_timestamp - $minutes * self::SECONDS_PER_MINUTE;
        return $this;
    }

    public function setSecond($second)
    {
        $this->_second = $second;
        return $this->_changeDate();
    }

    public function getSecond() { return $this->_second; }

    public function addSeconds($seconds)
    {
        $this->timestamp = $this->timestamp + $seconds;
        return $this;
    }

    public function subSeconds($seconds)
    {
        $this->timestamp = $this->timestamp - $seconds;
        return $this;
    }
}
