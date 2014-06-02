<?php

namespace gear\helpers;
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
        return $this;
        //return $this->_fillDate();
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
//        return $this->_fillDate();
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
    public function getDay()
    {
        return $this->owner->getDay($this);
    }
    
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
    public function getMonth($mode = 1)
    {
        return $this->owner->getMonth($this, $mode);
    }
    
    /**
     * Возвращает год
     * 
     * @access public
     * @return integer
     */
    public function getYear()
    {
        return $this->owner->getYear($this);
    }
    
    /**
     * Возвращает час
     * 
     * @access public
     * @return integer
     */
    public function getHour()
    {
        return $this->owner->getHour($this);
    }
    
    /**
     * Возвращает минуты
     * 
     * @access public
     * @return integer
     */
    public function getMinute()
    {
        return $this->owner->getMinute($this);
    }
    
    /**
     * Возвращает секунды
     * 
     * @access public
     * @return integer
     */
    public function getSecond()
    {
        return $this->owner->getSecond($this);
    }
    
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
    public function getDayOfWeek($mode = 1)
    {
        return $this->owner->getDayOfWeek($this, $mode);
    }

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

    public function setNatural($natural)
    {
        $this->_natural = (boolean)$natural;
        return $this;
    }

    public function getNatural() { return $this->_natural; }

    /**
     * Возвращает отформатированную по шаблону дату
     * 
     * @access public
     * @param string $format
     * @param bool $natural
     * @return
     */
    public function format($format = null, $natural = false)
    {
        return $this->_value = $this->_calculate($format ? $format : $this->format, $natural);
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
     * @static
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
}
