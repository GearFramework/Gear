<?php

namespace Gear\Helpers;

use Gear\Core;
use Gear\Library\Calendar\GLocale;
use Gear\Library\GHelper;
use Gear\Models\Calendar\GDate;
use Gear\Models\Calendar\GTimeInterval;
use Gear\Traits\Factory\StaticFactoryTrait;

/**
 * Хелпер для работы с календарем
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class CalendarHelper extends GHelper
{
    /* Traits */
    use StaticFactoryTrait;
    /* Const */
    const SECONDS_PER_MIN = 60;
    const SECONDS_PER_HOUR = self::SECONDS_PER_MIN * 60;
    const SECONDS_PER_DAY = self::SECONDS_PER_HOUR * 24;
    const SECONDS_PER_WEEK = self::SECONDS_PER_DAY * 7;
    const SECONDS_PER_ODD_MONTH = self::SECONDS_PER_DAY * 31;
    const SECONDS_PER_EVEN_MONTH = self::SECONDS_PER_DAY * 30;
    const SECONDS_PER_LEAP_MONTH = self::SECONDS_PER_DAY * 29;
    const SECONDS_PER_NOT_LEAP_MONTH = self::SECONDS_PER_DAY * 28;
    const SECONDS_PER_LEAP_YEAR = self::SECONDS_PER_LEAP_MONTH + (self::SECONDS_PER_ODD_MONTH * 7) + (self::SECONDS_PER_EVEN_MONTH * 4);
    const SECONDS_PER_NOT_LEAP_YEAR = self::SECONDS_PER_NOT_LEAP_MONTH + (self::SECONDS_PER_ODD_MONTH * 7) + (self::SECONDS_PER_EVEN_MONTH * 4);
    /* Private */
    /* Protected */
    protected static array $_config = [
        'options' => [
            'format' => 'Y-m-d H:i:s',
            'asUT' => false,
        ],
    ];
    protected static $_factoryProperties = [
        'class' => '\Gear\Models\Calendar\GDate',
    ];
    protected static $_model = [
        'class' => '\Gear\Models\Calendar\GDate',
    ];
    protected static $_currentDate = null;
    protected static $_locale = null;
    protected static $_namespaceLocales = '\Gear\Models\Calendar\Locales';
    /* Public */

    /**
     * Возвращает установленную локаль
     *
     * @return null|GLocale
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getLocale(): ?GLocale
    {
        if (!self::$_locale && Core::props('locale')) {
            $localeClass = self::$_namespaceLocales . '\\' . Core::props('locale');
            self::$_locale = new $localeClass();
        }
        return self::$_locale;
    }

    /**
     * Возвращает количество дней в месяце
     *
     * @param GDate|null $date
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function countDays(?GDate $date = null): int
    {
        if (!$date) {
            $date = self::helpNow();
        }
        return (int)date('t', $date->timestamp);
    }

    /**
     * Возвращает объект текущей даты или указанной в виде строки или UT
     *
     * @param null|string|int $date
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpDate($date = null): GDate
    {
        if (null === $date) {
            $date = time();
        } elseif (is_string($date)) {
            $date = strtotime($date);
        }
        if (!is_int($date)) {
            throw Core::CalendarException('Invalid date <{date}>', ['date' => $date]);
        }
        return self::factory(['timestamp' => $date, 'locale' => self::getLocale()]);
    }

    /**
     * Возвращает день
     *
     * @param null $date
     * @param array $options
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpDay($date = null, $options = ['format' => 'd'])
    {
        /** @var GDate $date */
        $date = self::helpDate($date);
        return $date->day($options);
    }

    /**
     * Возвращает дату первого дня месяца
     *
     * @param GDate|null $date
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpFirstDate(?GDate $date = null): GDate
    {
        if (!$date) {
            $date = self::helpNow();
        }
        return self::helpMakeTime(0, 0, 0, $date->month, 1, $date->year);
    }

    /**
     * @param int $seconds
     * @return GTimeInterval
     * @since 0.0.2
     * @version 0.0.2
     */
    public function helpInterval(int $seconds): GTimeInterval
    {
        return new GTimeInterval(['interval' => $seconds]);
    }

    /**
     * Возвращает true, если год високосный
     *
     * @param null|int|string|GDate $date
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function helpIsLeap($date = null): bool
    {
        $date = $date ? self::helpDate($date) : self::helpNow();
        return date('L') ? true : false;
    }

    /**
     * Возвращает дату первого дня месяца
     *
     * @param GDate|null $date
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpLastDate(?GDate $date = null): GDate
    {
        if (!$date) {
            $date = self::helpNow();
        }
        return self::helpMakeTime(0, 0, 0, $date->month, date('t', $date->timestamp), $date->year);
    }

    /**
     * Возвращает метку времени Unix для заданной даты
     *
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param int $month
     * @param int $day
     * @param int $year
     * @return GDate
     * @since 0.0.2
     * @version 0.0.2
     */
    public function helpMakeTime(int $hour = 0, int $minute = 0, int $second = 0, int $month = 0, int $day = 0, $year = 0): GDate
    {
        $tm = mktime($hour, $minute, $second, $month, $day, $year);
        return self::helpDate($tm);
    }

    /**
     * Возвращает месяц
     *
     * @param null $date
     * @param array $options
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpMonth($date = null, $options = ['format' => 'm'])
    {
        $date = self::date($date);
        $options = self::_prepareOptions($options);
        return $date->month($options);
    }

    /**
     * Возвращает текущее время
     *
     * @return GDate
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpNow(): GDate
    {
        self::setDate(time());
        return self::$_currentDate;
    }

    /**
     * Возвращает год
     *
     * @param null $date
     * @param array $options
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpYear($date = null, $options = ['format' => 'Y']): int
    {
        $date = self::date($date);
        $options = self::_prepareOptions($options);
        return $date->year($options);
    }

    /**
     * Установка текущей даты в календаре
     *
     * @param int|string|GDate $date
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function setDate($date)
    {
        self::$_currentDate = self::date($date);
    }
}
