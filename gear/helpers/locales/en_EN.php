<?php

namespace gear\helpers\locales;

/**
 * Английская локаль
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 27.05.2014
 */
class en_EN
{
    /* Const */
    const NOW = 0;
    const LESS_MIN_PAST = 1;
    const ONE_MIN_PAST = 2;
    const MIN_PAST = 3;
    /* Private */
    /* Protected */
    protected static $_human = [];
    protected static $_data = 
    [
        'month' => 
        [
            'short' => [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sen', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'],
            'full' => [1 => ['January', 'January'], 2 => ['February', 'February'], 3 => ['March', 'March'], 4 => ['April', 'April'], 5 => ['May', 'May'], 6 => ['Juny', 'Juny'], 7 => ['July', 'July'], 8 => ['August', 'August'], 9 => ['September', 'September'], 10 => ['October', 'October'], 11 => ['November', 'November'], 12 => ['December', 'December']],
        ],
        'week' =>
        [
            'short' => [0 => 'Su', 1 => 'Mo', 2 => 'Tu', 3 => 'We', 4 => 'Th', 5 => 'Fr', 6 => 'Sa'],
            'full' => [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'],
        ],
    ];
    protected static $_sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    /* Public */
    public static $registerTokens = ['d', 'D', 'j', 'l', 'M', 'F', 'w'];

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
    public static function getTokenValue($token, $time, $natural) {
        switch($token) {
            case 'D' : return self::getShortWeek($time);
            case 'l' : return self::getFullWeek($time);
            case 'M' : return self::getShortMonth($time);
            case 'F' : return self::getFullMonth($time, $natural);
            case 'w' : return ($dayOfWeek = date($token, $time)) ? $dayOfWeek : 7;
            case 'd' : return date(($natural ? 'jS' : 'd'), $time);
            case 'j' : return date('j' . ($natural ? 'S' : ''), $time);
        }
    }

    /**
     * Возвращает количество дней в месяце
     *
     * @access public
     * @param integer $timestamp
     * @return integer
     */
    public static function getCountDaysInMonth($timestamp) {
        return date('t', $timestamp);
    }

    /**
     * Возвращает количество дней в году
     *
     * @access public
     * @param integer $timestamp
     * @return integer
     */
    public static function getCountDaysInYear($timestamp) {
        return date('L', $timestamp) ? 366 : 365;
    }
}
