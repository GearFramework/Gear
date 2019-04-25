<?php

namespace Gear\Models\Calendar;

use Gear\Library\GModel;

/**
 * Модель временного интервала
 *
 * @package Gear Framework
 *
 * @property int days
 * @property string format
 * @property int hours
 * @property int interval
 * @property int minutes
 * @property int months
 * @property int seconds
 * @property int years
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
    protected $_format = '%02H:%02I:%02S';
    protected $_interval = 0;
    /* Public */

    public function __toString(): string
    {
        $tokens = str_split($this->format);
        $tokensDefault = ['Y' => 'years','M' => 'months','D' => 'days','H' => 'hours','I' => 'minutes','S' => 'seconds'];
        $resultFormat = '';
        $isDig = false;
        $printf = '';
        foreach ($tokens as $token) {
            if ($token === '%') {
                $printf = $token;
                $isDig = true;
            } else {
                if ($isDig && is_numeric($token)) {
                    $printf .= $token;
                } elseif ($isDig && isset($tokensDefault[$token])) {
                    $isDig = false;
                    $resultFormat .= sprintf($printf . "d", $this->{$tokensDefault[$token]});
                    $printf = '';
                } elseif (isset($tokensDefault[$token])) {
                    $resultFormat .= sprintf('%d', $this->{$tokensDefault[$token]});
                    $printf = '';
                } else {
                    $resultFormat .= $token;
                }
            }
        }
        return $resultFormat;
    }

    public function addInterval($interval): GTimeInterval
    {
        $addValue = $interval instanceof GTimeInterval ? $interval->getInterval() : (int)$interval;
        $this->interval = $this->interval + $addValue;
        return $this;
    }

    public function format(string $format): GTimeInterval
    {
        $this->format = $format;
        return $this;
    }

    public function getFormat(): string
    {
        return $this->_format;
    }

    public function getInterval(): int
    {
        return $this->_interval;
    }

    public function setFormat(string $format)
    {
        $this->_format = $format;
    }

    public function setInterval(int $interval)
    {
        $this->_interval = $interval;
        $periods = [31536000, 2678400, 86400, 3600, 60];
        $isZero = false;
        $times = [0, 0, 0, 0, 0];
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