<?php

namespace gear\helpers\locales;

/**
 * Русская локаль
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 27.05.2014
 */
abstract class GLocale
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    /**
     * Склонение числительных годов, месяцев, дней, часов, минут, секунд
     * 
     * @access public
     * @static
     * @param integer $value
     * @param string $mode любое из: diff
     * @param string $token любое из: y, m, d, h, i, s
     * @return string
     */
    public static function getDecline($value, $mode, $token)
    {
        $keys = array(2, 0, 1, 1, 1, 2);
        $mod = $value % 100;
        $key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
        return static::$_words[$mode][$token][$key];
    }

    /**
     * Получение локализованных значений элементов шаблона даты
     * 
     * @access public
     * @static
     * @param string $token
     * @param integer $timestamp
     * @param boolean $natural
     * @return string
     */
    public static function getTokenValue($token, $timestamp, $natural)
    {
        switch($token)
        {
            case 'D' : return static::getShortWeek($time);
            case 'l' : return static::getFullWeek($time);
            case 'M' : return static::getShortMonth($time);
            case 'F' : return static::getFullMonth($time, $natural);
            case 'w' : return ($dayOfWeek = date($token, $time)) ? $dayOfWeek : 7;
        }
    }
    
    public static function getHuman($seconds, $mode, $short)
    {
        if (isset(static::$_human[$mode][$short][2]))
        {
            
        }
        return sprintf(self::$_human[$mode][$short], abs($seconds));
    }

    /**
     * Возвращает короткое название дня недели для указанной даты
     * 
     * @access public
     * @static
     * @param integer $timestamp
     * @return string
     */
    public static function getShortWeek($timestamp) { return static::$_data['week']['short'][(int)date('w', $timestamp)]; }

    /**
     * Возвращает полное название дня недели для указанной даты
     * 
     * @access public
     * @static
     * @param integer $timestamp
     * @return string
     */
    public static function getFullWeek($timestamp) { return static::$_data['week']['full'][(int)date('w', $timestamp)]; }

    /**
     * Возвращает короткое название месяца для указанной даты
     * 
     * @access public
     * @static
     * @param integer $timestamp
     * @return string
     */
    public static function getShortMonth($timestamp) { return static::$_data['month']['short'][(int)date('n', $timestamp)]; }

    /**
     * Возвращает полное название месяца для указанной даты
     * 
     * @access public
     * @static
     * @param integer $timestamp
     * @return string
     */
    public static function getFullMonth($timestamp, $natural = 0) { return static::$_data['month']['full'][(int)date('n', $timestamp)][$natural]; }

    /**
     * Возвращает массив коротких названий дней недели
     * 
     * @access public
     * @static
     * @return array
     */
    public static function getShortWeeks() { return static::$_data['week']['short']; }

    /**
     * Возвращает массив полных названий дней недели
     * 
     * @access public
     * @static
     * @return array
     */
    public static function getFullWeeks() { return static::$_data['week']['full']; }

    /**
     * Возвращает массив коротких названий месяцев
     * 
     * @access public
     * @static
     * @return array
     */
    public static function getShortMonths() { return static::$_data['month']['short']; }

    /**
     * Возвращает массив полных названий месяцев
     * 
     * @access public
     * @static
     * @return array
     */
    public static function getFullMonths()
    {
        $months = array();
        foreach(static::$_data['month']['full'] as $month)
            $months[] = $month[0];
        return $months;
    }

    /**
     * Возвращает номер дня недели
     * 
     * @access public
     * @static
     * @return array
     */
    public static function getNumberDayOfWeek($timestamp) { return (int)date('w', $timestamp); }
    
    /**
     * Возвращает номер первого дня недели
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getFirstNumberDayOfWeek() { return 0; }
    
    /**
     * Возвращает номер последнего дня недели
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getLastNumberDayOfWeek() { return 6; }

    /**
     * Возвращает массив номеров дней недели
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getNumbersDayOfWeek() { return range(0, 6); }
    
    /**
     * Возвращает номер первого дня года
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getFirstDayOfYear() { return 1; }
    
    /**
     * Возвращает номер последнего дня года
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getLastDayOfYear() { return 31; }
    
    /**
     * Возвращает номер первого месяца года
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getFirstMonthOfYear() { return 1; }
    
    /**
     * Возвращает номер последнего дня года
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getLastMonthOfYear() { return 12; }
}
