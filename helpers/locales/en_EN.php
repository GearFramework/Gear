<?php

namespace gear\helpers\locales;

/**
 * Английская локаль
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
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
    private static $_human = array
    (
    );
    private static $_data = array
    (
        'month' => array
        (
            'short' => array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sen', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'),
            'full' => array(1 => array('January', 'January'), 2 => array('February', 'February'), 3 => array('March', 'March'), 4 => array('April', 'April'), 5 => array('May', 'May'), 6 => array('Juny', 'Juny'), 7 => array('July', 'July'), 8 => array('August', 'August'), 9 => array('September', 'September'), 10 => array('October', 'October'), 11 => array('November', 'November'), 12 => array('December', 'December')),
        ),
        'week' => array
        (
            'short' => array(0 => 'Su', 1 => 'Mo', 2 => 'Tu', 3 => 'We', 4 => 'Th', 5 => 'Fr', 6 => 'Sa'),
            'full' => array(0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'),
        ),
    );
    /* Protected */
    /* Public */
    public static $registerTokens = array('d', 'D', 'j', 'l', 'M', 'F', 'w');

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
    public static function getTokenValue($token, $time, $natural)
    {
        switch($token)
        {
            case 'D' : return self::getShortWeek($time);
            case 'l' : return self::getFullWeek($time);
            case 'M' : return self::getShortMonth($time);
            case 'F' : return self::getFullMonth($time, $natural);
            case 'w' : return ($dayOfWeek = date($token, $time)) ? $dayOfWeek : 7;
            case 'd' : return date(($natural ? 'jS' : 'd'), $time);
            case 'j' : return date('j' . ($natural ? 'S' : ''), $time);
        }
    }
}
