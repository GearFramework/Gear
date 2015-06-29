<?php

namespace gear\models;
use gear\Core;
use gear\library\GModel;

/**
 * Директория
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 02.06.2014
 * @php 5.3.x
 */
class GDate extends GModel
{
    /* Const */
    const SECONDS_PER_MINUTE = 60;
    const SECONDS_PER_HOUR = 3600;
    const SECONDS_PER_DAY = 86400;
    /* Private */
    /* Protected */
    /* Формат вывода даты */
    protected $_format = 'Y-m-d H:i:s';
    protected $_natural = false;
    /* Дата и время в формате Y-m-d H:i:s */
    protected $_datetime = null;
    /* Unix-timestamp */
    protected $_timestamp = 0;
    protected $_value = null;
    /* Public */

    /**
     * Вывод отформатированой даты
     *
     * @access public
     * @return string
     */
    public function __toString() { return $this->format($this->format); }

    /**
     * Возвращает владельца даты - календарь
     *
     * @access public
     * @return object
     */
    public function getOwner() { return $this->_owner; }

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
        return $this;
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
        return $this;
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
     * Возвращает день
     * 
     * @access public
     * @return integer
     */
//    public function getDay() { return $this->owner->getDay($this); }
    
    /**
     * Возвращает месяц
     * 
     * Значения для $mode
     * 
     * 1 - возращает порядковый номер месяца
     * 2 - возвращает полное название месяца
     * 3 - возвращает сокращённое название месяца
     * 
     * @access public
     * @param integer $mode
     * @return integer
     */
//    public function getMonth($mode = 1) { return $this->owner->getMonth($this, $mode); }
    
    /**
     * Возвращает год
     * 
     * @access public
     * @return integer
     */
    public function getYear() { return date('Y', $this->timestamp); }
    
    /**
     * Возвращает час
     * 
     * @access public
     * @return integer
     */
//    public function getHour() { return $this->owner->getHour($this); }
    
    /**
     * Возвращает минуты
     * 
     * @access public
     * @return integer
     */
//    public function getMinute() { return $this->owner->getMinute($this); }
    
    /**
     * Возвращает секунды
     * 
     * @access public
     * @return integer
     */
//    public function getSecond() { return $this->owner->getSecond($this); }
    
    /**
     * Возвращает день недели
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
//    public function getDayOfWeek($mode = 1) { return $this->owner->getDayOfWeek($this, $mode); }

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
     * @return integer
     */
    public function getQuarter()
    {
        return $this->owner->getQuarter($this);
    }

    /**
     * Возвращает отформатированную по шаблону дату
     * 
     * @access public
     * @param string $format
     * @param bool $natural
     * @return
     */
    public function format($format = null, $natural = null)
    {
        return $this->_value = $this->_calculate($format ? $format : $this->format, $natural !== null ? $natural : $this->natural);
    }
    
    /**
     * Возвращает разницу между указанной датой и текущей в 
     * "человекопонятном стиле"
     * 
     * @access public
     * @return string
     */
    public function humanDiff($date = null)
    {
        return $this->owner->humanDiff($this, $date);
    }
    
    /**
     * Вычисление разницы между датами
     * 
     * @access public
     * @param integer|string|object $date
     * @return string
     */
    public function diff($date, $format = 'y m d h i s')
    {
        $diff = $this->owner->diff($this, $date);
        $class = $this->getLocaleNamespace() . '\\' . $this->getLocale();
        $tokens = array('y', 'm', 'd', 'h', 'i', 's');
        if ($format)
        {
            $resultDiff = '';
            foreach(preg_split('//', $format, 0, PREG_SPLIT_NO_EMPTY) as $token)
                $resultDiff .= ($index = array_search($token, $tokens)) !== false ? $diff[$index] . ' ' . $class::getDecline($diff[$index], 'diff', $token) : $token;
            return $resultDiff;
        }
        else
            return array_combine($tokens, $diff);
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

    /**
     * Форматирование даты по указанному шаблону
     *
     * @access private
     * @param integer|string $time
     * @param string $format
     * @param bool $natural
     * @return string
     */
    private function _calculate($format, $natural)
    {
        try
        {
            $class = $this->getLocaleNamespace() . '\\' . $this->getLocale();
            $defaultTokens = array('a', 'A', 'B', 'c', 'd', 'e', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'L', 'm', 'n', 'N', 'o', 'O', 'P', 'r', 's', 'S', 't', 'T', 'u', 'U', 'W', 'y', 'Y', 'z', 'Z');
            $natural = (int)$natural ? 1 : 0;
            $result = '';
            foreach(preg_split('//', $format, 0, PREG_SPLIT_NO_EMPTY) as $token)
            {
                if (in_array($token, $class::$registerTokens, true))
                    $result .= $class::getTokenValue($token, $this->timestamp, $natural);
                else
                if (in_array($token, $defaultTokens, true))
                    $result .= date($token, $this->timestamp);
                else
                    $result .= $token;
            }
            return $result;
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
}
