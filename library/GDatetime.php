<?php

namespace gear\library;
use \gear\Core as Core;
use \gear\library\GObject as GObject;

class GDatetime extends GObject
{
    public static $mon = array
        (
            'short' => array
                (
                    1 => 'Янв',
                    2 => 'Фев',
                    3 => 'Мрт',
                    4 => 'Апр',
                    5 => 'Май',
                    6 => 'Июн',
                    7 => 'Июл',
                    8 => 'Авг',
                    9 => 'Сен',
                    10 => 'Окт',
                    11 => 'Ноя',
                    12 => 'Дек',
                ),
            'full' => array
                (
                    1 => array('Январь', 'Января'),
                    2 => array('Февраль', 'Февраля'),
                    3 => array('Март', 'Марта'),
                    4 => array('Апрель', 'Апреля'),
                    5 => array('Май', 'Мая'),
                    6 => array('Июнь', 'Июня'),
                    7 => array('Июль', 'Июля'),
                    8 => array('Август', 'Августа'),
                    9 => array('Сентябрь', 'Сентября'),
                    10 => array('Октябрь', 'Октября'),
                    11 => array('Ноябрь', 'Ноября'),
                    12 => array('Декабрь', 'Декабря'),
                )
        );
    public static $week = array
        (
            'short' => array
                (
                    0 => 'Вс',
                    1 => 'Пн',
                    2 => 'Вт',
                    3 => 'Ср',
                    4 => 'Чт',
                    5 => 'Пт',
                    6 => 'Сб',
                ),
            'full' => array
                (
                    0 => 'Воскресение',
                    1 => 'Понедельник',
                    2 => 'Вторник',
                    3 => 'Среда',
                    4 => 'Четверг',
                    5 => 'Пятница',
                    6 => 'Суббота',
                ),
        );
    public static $secperday = 86400;

    public static function format($time, $format, $natural = 0)
    {
        if (!is_numeric($time))
            $time = strtotime($time);
        $natural = $natural ? 1 : 0;
        $chunk = preg_split('//', $format, 0, PREG_SPLIT_NO_EMPTY);
        $result = '';
        foreach($chunk as $op)
        {
            switch($op)
            {
                case 'd' :
                case 'j' :
                case 'N' :
                case 'S' :
                case 'w' :
                case 'W' :
                case 'm' :
                case 'n' :
                case 't' :
                case 'L' :
                case 'o' :
                case 'Y' :
                case 'y' :
                case 'a' :
                case 'A' :
                case 'B' :
                case 'g' :
                case 'G' :
                case 'h' :
                case 'H' :
                case 'i' :
                case 's' :
                case 'u' : $result .= date($op, $time); break;
                case 'D' :
                {
                    $result .= self::$week['short'][date('w', $time)];
                    break;
                }
                case 'l' :
                {
                    $result .= self::$week['full'][date('w', $time)];
                    break;
                }
                case 'M' :
                {
                    $result .= self::$mon['short'][date('n', $time)];
                    break;
                }
                case 'F' :
                {
                    $result .= self::$mon['full'][date('n', $time)][$natural];
                    break;
                }
                default : $result .= $op; break;
            }
        }
        return $result;
    }

    public static function humanDiff($time, $datesec)
    {
        if (!is_numeric($time))
            $time = strtotime($time);
        if (!is_numeric($datesec))
            $datesec = strtotime($datesec);
        $diff = abs($time - $datesec);
        if ($diff < 60)
            return $diff . ' сек.';
        else
        if ($diff < 3600)
            return (int)($diff / 60) . ' мин.';
        else
        if ($diff >= 3600 && $diff < 7200)
            return '1 час';
        else
        if ($diff >= 7200 && $diff <= 4 * 3600)
            return 'более часа';
        else
        if ($diff > 23 * 3600 && $diff <= self::$secperday)
            return 'сутки';
        else
        if ($diff > self::$secperday && $diff < 2 * self::$secperday)
            return 'более суток';
        else
        if ($diff > 2 * self::$secperday)
            return (int)($diff / self::$secperday) . 'дн.';
    }

    public static function human($time, $format = null)
    {
        if (!is_numeric($time))
            $time = strtotime($time);
        $time = date('H:i', $time);
        $datein = date('Ymd', $time);
        $now = date('Ymd', time());
        $yesterday = date('Ymd', time() - self::$secperday);
        $postyesterday = date('Ymd', time() - (self::$secperday * 2));
        $tomorrow = date('Ymd', time() + self::$secperday);
        $beforetomorrow = date('Ymd', time() + (self::$secperday * 2));
        if ($datein == $now)
            return 'Сегодня, ' . $time;
        else
        if ($datein === $yesterday)
            return 'Вчера, ' . $time;
        else
        if ($datein === $postyesterday)
            return 'Позавчера, ' . $time;
        else
        if ($datein === $tomorrow)
            return 'Завтра, ' . $time;
        else
        if ($datein === $beforetomorrow)
            return 'Поcлезавтра, ' . $time;
        else
        {
            if (empty($format))
                return self::format('D, d F Y H:i', $time);
            else
                return self::format($format, $time);
        }
    }
}
