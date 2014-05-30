<?php

namespace gear\helpers\datetime;

class ru_RU
{
    /* Const */
    /* Private */
    private static $_data = array
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

    public static function getTokenValue($token, $time, $natural)
    {
        switch($token)
        {
            case 'D' : return self::getShortWeek($time);
            case 'l' : return self::getFulltWeek($time);
            case 'M' : return self::getShortMonth($time);
            case 'F' : return self::getFullMonth($time);
            case 'w' : return ($dayOfWeek = date($token, $time)) ? $dayOfWeek : 7;
        }
    }

    public static function getShortWeek($time)
    {
        $dw = (int)date('w', $time);
        if (!$dw)
            $dw = 7;
        return self::$_data['week']['short'][$dw];
    }

    public static function getFullWeek($time)
    {
        $dw = (int)date('w', $time);
        if (!$dw)
            $dw = 7;
        return self::$_data['week']['full'][$dw];
    }

    public static function getShortMonth($time)
    {
        return self::$_data['month']['short'][(int)date('n', $time)];
    }

    public static function getFullMonth($time, $natural)
    {
        return self::$_data['month']['full'][(int)date('n', $time)][$natural];
    }

    public static function getShortWeeks()
    {
        return self::$_data['week']['short'];
    }

    public static function getFullWeeks()
    {
        return self::$_data['week']['full'];
    }

    public static function getShortMonths()
    {
        return self::$_data['month']['short'];
    }

    public static function getFullMonths($time, $natural)
    {
        $months = array();
        foreach(self::$_data['month']['full'] as $month)
            $months[] = $month[0];
        return $months;
    }

    public static function getNumberDayOfWeek($time)
    {
        $dayOfWeek = date('w', $time);
        return $dayOfWeek ? $dayOfWeek : 7;
    }
}
