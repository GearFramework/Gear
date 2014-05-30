<?php

namespace gear\models;
use gear\Core;
use gear\library\GModel;

class GDate extends GModel
{
    /* Const */
    const SECONDS_PER_MINUTE = 60;
    const SECONDS_PER_HOUR = 3600;
    const SECONDS_PER_DAY = 86400;
    /* Private */
    /* Protected */
    protected $_datetime = null;
    protected $_timestamp = 0;
    protected $_day = 0;
    protected $_month = 0;
    protected $_year = 0;
    protected $_hour = 0;
    protected $_minute = 0;
    protected $_second = 0;
    protected $_format = 'Y-m-d H:i:s';
    protected $_natural = false;
    protected $_value = null;
    /* Public */

    /**
     * Форматирование даты
     *
     * @access public
     * @return string
     */
    public function __toString() { return $this->format($this->format); }

    /**
     * Установка даты и времени в формате, понимаемом функцией strtotime()
     *
     * @access public
     * @param string $datetime
     * @return $this
     */
    public function setDatetime($datetime)
    {
        $this->_datetime = $datetime;
        $this->_timestamp = strtotime($this->_datetime);
        return $this->_fillDate();
    }

    /**
     * Получение даты и времени в формате, понимаемом функцией strtotime()
     *
     * @access public
     * @return string
     */
    public function getDatetime()
    {
        if (!$this->_datetime)
            $this->_datetime = date('Y-m-d H:i:s', $this->_timestamp);
        return $this->_datetime;
    }

    /**
     * Установка временной метки UNIX
     *
     * @access public
     * @param integer $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->_timestamp = $timestamp;
        $this->datetime = date('Y-m-d H:i:s', $this->_timestamp);
        return $this->_fillDate();
    }

    /**
     * Получение временной метки UNIX
     *
     * @access public
     * @return integer
     */
    public function getTimestamp()
    {
        if (!$this->_timestamp)
            $this->_timestamp = $this->datetime ? strtotime($this->datetime) : time();
        return $this->_timestamp;
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

    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    public function getFormat() { return $this->_format; }

    public function setNatural($natural)
    {
        $this->_natural = (boolean)$natural;
        return $this;
    }

    public function getNatural() { return $this->_natural; }

    public function format($format = null)
    {
        return $this->_value = \gear\helpers\GDatetime::format($this->timestamp, $format ? $format : $this->format, $this->natural);
    }

    public function firstDayOfWeek()
    {
        return \gear\helpers\GDatetime::firstDayOfWeek($this->timestamp);
    }

    public function getWeeks()
    {
        return \gear\helpers\GDatetime::getWeeks();
    }

    public function onConstructed()
    {
        parent::onConstructed();
        if ($this->_datetime)
            $this->timestamp = strtotime($this->datetime);
        else
        if ($this->_timestamp)
            $this->datetime =  $this->format($this->timestamp, $this->format);
        else
            $this->timestamp = time();
    }

    private function _fillDate()
    {
        $this->_day = date('d', $this->_timestamp);
        $this->_month = date('m', $this->_timestamp);
        $this->_year = date('Y', $this->_timestamp);
        $this->_hour = date('H', $this->_timestamp);
        $this->_minute = date('i', $this->_timestamp);
        $this->_second = date('s', $this->_timestamp);
        $this->_value = null;
        return $this;
    }

    private function _changeDate()
    {
        $this->_timestamp = mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
        $this->_datetime = date('Y-m-d H:i:s', $this->_timestamp);
        return $this;
    }
}
