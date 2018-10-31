<?php

namespace Gear\Helpers;

use Gear\Core;
use Gear\Library\Calendar\GLocale;
use Gear\Library\GHelper;
use Gear\Models\GDate;
use Gear\Traits\TStaticFactory;

/**
 * Хелпер для работы с календарем
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class HCalendar extends GHelper
{
    /* Traits */
    use TStaticFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'options' => [
            'format' => 'Y-m-d H:i:s',
            'asUT' => false,
        ],
    ];
    protected static $_factoryProperties = [
        'class' => '\Gear\Models\Calendar\GDate',
    ];
    protected static $_currentDate = null;
    protected static $_locale = null;
    protected static $_namespaceLocales = '\Gear\Models\Calendar\Locales';
    /* Public */

    public static function getLocale(): GLocale
    {
        if (!self::$_locale) {
            $localeClass = self::$_namespaceLocales . '\\' . Core::props('locale');
            self::$_locale = new $localeClass();
        }
        return self::$_locale;
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
        $date = self::date($date);
        $options = self::_prepareOptions($options);
        return $date->day($options);
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
        self::$_currentDate = self::date(time());
        return self::$_currentDate;
    }

    /**
     * Установка текущей даты в календаре
     *
     * @param int|string|GDate $date
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function setDate($date)
    {
        self::$_currentDate = self::date($date);
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
}
