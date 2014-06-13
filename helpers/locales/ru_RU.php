<?php

namespace gear\helpers\locales;
use gear\helpers\locales\GLocale;

/**
 * Русская локаль
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 27.05.2014
 */
class ru_RU extends GLocale
{
    /* Const */
    const NOW = 0;
    const LESS_MIN_PAST = 1;
    const ONE_MIN_PAST = 2;
    const MIN_PAST = 3;
    /* Private */
    protected static $_human = array
    (
        'now' => array('сейчас'),
        'past' => array('назад', '%s назад', '%d %s назад'),
        'future' => array('через', 'через %s', 'через %d %s'),
    );
    protected static $_words = array
    (
        'diff' => array
        (
            'y' => array('год', 'года', 'лет'),
            'm' => array('месяц', 'месяца', 'месяцев'),
            'd' => array('день', 'дня', 'дней'),
            'h' => array('час', 'часа', 'часов'),
            'i' => array('минуту', 'минуты', 'минут'),
            's' => array('секунду', 'секунды', 'секунд'),
            'w' => array('неделю', 'недели', 'недель'),
        ),
    );
    protected static $_data = array
    (
        'month' => array
        (
            'short' => array(1 => 'Янв', 2 => 'Фев', 3 => 'Мрт', 4 => 'Апр', 5 => 'Май', 6 => 'Июн', 7 => 'Июл', 8 => 'Авг', 9 => 'Сен', 10 => 'Окт', 11 => 'Ноя', 12 => 'Дек'),
            'full' => array(1 => array('Январь', 'Января'), 2 => array('Февраль', 'Февраля'), 3 => array('Март', 'Марта'), 4 => array('Апрель', 'Апреля'), 5 => array('Май', 'Мая'), 6 => array('Июнь', 'Июня'), 7 => array('Июль', 'Июля'), 8 => array('Август', 'Августа'), 9 => array('Сентябрь', 'Сентября'), 10 => array('Октябрь', 'Октября'), 11 => array('Ноябрь', 'Ноября'), 12 => array('Декабрь', 'Декабря')),
        ),
        'week' => array
        (
            'short' => array(1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 7 => 'Вс'),
            'full' => array(1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четверг', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресение'),
        ),
    );
    /* Protected */
    /* Public */
    public static $registerTokens = array('D', 'l', 'M', 'F', 'w');
    
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
    
    public static function getHumanDecline($value, $mode, $token)
    {
        $decline = self::getDecline(abs($value), $mode, $token);
        if ($value == 1)
            return sprintf(static::$_human['future'][1], $decline);
        else
        if ($value > 1)
            return sprintf(static::$_human['future'][2], $value, $decline);
        else
        if ($value == -1)
            return sprintf(static::$_human['past'][1], $decline);
        else
        if ($value < -1)
            return sprintf(static::$_human['past'][2], abs($value), $decline);
        else
            return static::$_human['now'][0];
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
            case 'D' : return static::getShortWeek($timestamp);
            case 'l' : return static::getFullWeek($timestamp);
            case 'M' : return static::getShortMonth($timestamp);
            case 'F' : return static::getFullMonth($timestamp, $natural);
            case 'w' : return ($dayOfWeek = date($token, $timestamp)) ? $dayOfWeek : 7;
        }
    }
    
    public static function getHuman($seconds, $mode, $short)
    {
        if (isset(static::$_human[$mode][$short][2]))
        {
            
        }
        return sprintf(static::$_human[$mode][$short], abs($seconds));
    }

    /**
     * Возвращает короткое название дня недели для указанной даты
     * 
     * @access public
     * @static
     * @param integer $timestamp
     * @return string
     */
    public static function getShortWeek($timestamp)
    {
        $dw = (int)date('w', $timestamp);
        if (!$dw)
            $dw = 7;
        return static::$_data['week']['short'][$dw];
    }

    /**
     * Возвращает полное название дня недели для указанной даты
     * 
     * @access public
     * @static
     * @param integer $timestamp
     * @return string
     */
    public static function getFullWeek($timestamp)
    {
        $dw = (int)date('w', $timestamp);
        if (!$dw)
            $dw = 7;
        return static::$_data['week']['full'][$dw];
    }

    /**
     * Возвращает номер дня недели
     * 
     * @access public
     * @static
     * @return array
     */
    public static function getNumberDayOfWeek($timestamp)
    {
        $dayOfWeek = date('w', $timestamp);
        return $dayOfWeek ? $dayOfWeek : 7;
    }
    
    /**
     * Возвращает номер первого дня недели
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getFirstNumberDayOfWeek() { return 1; }
    
    /**
     * Возвращает номер последнего дня недели
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getLastNumberDayOfWeek() { return 7; }

    /**
     * Возвращает массив номеров дней недели
     * 
     * @access public
     * @static
     * @return integer
     */
    public static function getNumbersDayOfWeek() { return range(1, 7); }
}
