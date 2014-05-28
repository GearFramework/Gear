<?php

namespace \gear\models;
use gear\Core;
use gear\library\GModel;

class GDate extends GModel
{
    /* Const */
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
    /* Public */

    public function __toString()
    {
        return \gear\helpers\GDatetime::format($this->datetime ? $this->datetime : time(), $this->format, $this->_natural);
    }

    public function setDatetime($datetime)
    {
        $this->_datetime = $datetime;
    }

    public function getDatetime()
    {
        if (!$this->_datetime)
        {
            $this->_datetime = \gear\helpers\GDatetime::format
            (
                $this->timestamp ? $this->timestamp : time(),
                $this->format,
                $this->_natural
            );
        }
        return $this->_datetime;
    }

    public function setTimestamp($timestamp)
    {
        $this->_timestamp = (int)$timestamp;
    }

    public function getTimestamp()
    {
        if (!$this->_timestamp)
            $this->_timestamp = $this->datetime ? strtotime($this->datetime) : time();
        return $this->_timestamp;
    }

    public function setDay($day)
    {
        $this->_day = $day;
    }

    public function getDay()
    {
        return $day;
    }

    public function setMonth($month)
    {
        if ($this->datetime || $this->timestamp)
        $this->_month = $month;
    }

    public function getMonth()
    {
        return $this->_month;
    }

    public function setFormat($format) { $this->_format = $format; }

    public function getFormat($format) { return $this->_format; }

    public function format($format)
    {
        return \gear\helpers\GDatetime::format($this->datetime ? $this->datetime : time(), $format, $this->_natural);
    }

    public function onConstructed()
    {
        parent::onConstructed();
        if ($this->datetime)
            $this->timestamp = strtotime($this->datetime);
        else
        if ($this->timestamp)
            $this->datetime =  $this->format($this->timestamp, $this->format);
        else
            $this->e('Need specify the date');
    }
}
