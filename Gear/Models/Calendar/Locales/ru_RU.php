<?php

namespace Gear\Models\Calendar\Locales;

use Gear\Library\Calendar\GLocale;

/**
 * Русская локаль
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ru_RU extends GLocale
{
    /* Traits */
    /* Const */
    const NOW = 0;
    const LESS_MIN_PAST = 1;
    const ONE_MIN_PAST = 2;
    const MIN_PAST = 3;
    /* Private */
    /* Protected */
    protected static $_human = [
        'now' => ['сейчас'],
        'past' => ['назад', '%s назад', '%d %s назад'],
        'future' => ['через', 'через %s', 'через %d %s'],
    ];
    protected static $_words = [
        'diff' => [
            'y' => ['год', 'года', 'лет'],
            'm' => ['месяц', 'месяца', 'месяцев'],
            'd' => ['день', 'дня', 'дней'],
            'h' => ['час', 'часа', 'часов'],
            'i' => ['минуту', 'минуты', 'минут'],
            's' => ['секунду', 'секунды', 'секунд'],
            'w' => ['неделю', 'недели', 'недель'],
        ],
    ];
    protected static $_data = [
        'month' => [
            'short' => [1 => 'Янв', 2 => 'Фев', 3 => 'Мрт', 4 => 'Апр', 5 => 'Май', 6 => 'Июн', 7 => 'Июл', 8 => 'Авг', 9 => 'Сен', 10 => 'Окт', 11 => 'Ноя', 12 => 'Дек'],
            'full' => [1 => ['Январь', 'Января'], 2 => ['Февраль', 'Февраля'], 3 => ['Март', 'Марта'], 4 => ['Апрель', 'Апреля'], 5 => ['Май', 'Мая'], 6 => ['Июнь', 'Июня'], 7 => ['Июль', 'Июля'], 8 => ['Август', 'Августа'], 9 => ['Сентябрь', 'Сентября'], 10 => ['Октябрь', 'Октября'], 11 => ['Ноябрь', 'Ноября'], 12 => ['Декабрь', 'Декабря']],
        ],
        'week' => [
            'short' => [1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 7 => 'Вс'],
            'full' => [1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четверг', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресение'],
        ],
    ];
    protected static $_sizes = ['Б', 'КБ', 'МБ', 'ГБ', 'ТБ', 'ПБ'];
    /* Public */
    public static $registerTokens = ['D', 'l', 'M', 'F', 'w'];
    
    /**
     * Склонение числительных годов, месяцев, дней, часов, минут, секунд
     * 
     * @param integer $value
     * @param string $mode любое из: diff
     * @param string $token любое из: y, m, d, h, i, s
     * @return string
     * @since 0.0.1
     * @version 0.0.1
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
        elseif ($value > 1)
            return sprintf(static::$_human['future'][2], $value, $decline);
        elseif ($value == -1)
            return sprintf(static::$_human['past'][1], $decline);
        elseif ($value < -1)
            return sprintf(static::$_human['past'][2], abs($value), $decline);
        else
            return static::$_human['now'][0];
    }

    /**
     * Получение локализованных значений элементов шаблона даты
     * 
     * @param string $token
     * @param integer $timestamp
     * @param boolean $natural
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getTokenValue($token, $timestamp, $natural)
    {
        switch($token) {
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
     * @param integer $timestamp
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getShortWeek($timestamp)
    {
        $dw = (int)date('w', $timestamp);
        if (!$dw) {
            $dw = 7;
        }
        return static::$_data['week']['short'][$dw];
    }

    /**
     * Возвращает полное название дня недели для указанной даты
     * 
     * @param integer $timestamp
     * @return string
     * @since 0.0.1
     * @version 0.0.1
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
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getNumberDayOfWeek($timestamp)
    {
        $dayOfWeek = date('w', $timestamp);
        return $dayOfWeek ? $dayOfWeek : 7;
    }
    
    /**
     * Возвращает номер первого дня недели
     * 
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getFirstNumberDayOfWeek() { return 1; }
    
    /**
     * Возвращает номер последнего дня недели
     * 
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getLastNumberDayOfWeek() { return 7; }

    /**
     * Возвращает массив номеров дней недели
     * 
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getNumbersDayOfWeek() { return range(1, 7); }
}
