<?php

namespace Gear\Models\Calendar;

use Gear\Library\GModel;

/**
 * Модель временного интервала
 *
 * @package Gear Framework
 *
 * @property int days
 * @property int months
 * @property int years
 * @property int hours
 * @property int minutes
 * @property int seconds
 * @property int interval
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class GTimeInterval extends GModel
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_interval = 0;
    /* Public */

    public function __toString(): string
    {
        $f = [];
        $v = [];
        if ($this->years) {
            $f[] = '%d y.';
            $v[] = $this->years;
        }
        if ($this->months) {
            $f[] = '%d m.';
            $v[] = $this->months;
        }
        if ($this->days) {
            $f[] = '%d d.';
            $v[] = $this->days;
        }
        if ($this->hours) {
            $f[] = '%d h.';
            $v[] = $this->hours;
        }
        if ($this->minutes) {
            $f[] = '%d min.';
            $v[] = $this->minutes;
        }
        if ($this->seconds) {
            $f[] = '%d s.';
            $v[] = $this->seconds;
        }
        return printf(implode(' ', $f), ...$v);
    }

    public function getInterval(): int
    {
        return $this->_interval;
    }

    public function setInterval(int $interval)
    {
        $this->_interval = $interval;
        $periods = [31536000, 2678400, 86400, 3600, 60];
        $isZero = false;
        $times = [];
        foreach ($periods as $i => $p) {
            $period = floor($interval / $p);
            if (($period > 0) || ($period == 0 && $isZero)) {
                $times[$i] = $period;
                $interval -= $period * $periods[$i];
                $isZero = true;
            }
        }
        $times[] = $interval;
        list($this->years, $this->months, $this->days, $this->hours, $this->minutes, $this->seconds) = $times;
    }
}