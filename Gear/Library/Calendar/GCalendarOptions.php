<?php

namespace Gear\Library\Calendar;

use Gear\Traits\TGetter;
use Gear\Traits\TProperties;
use Gear\Traits\TSetter;

/**
 * Опции календаря
 *
 * @package Gear Framework
 *
 * @property string format
 * @property string humanize
 * @property string natural
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GCalendarOptions
{
    /* Traits */
    use TProperties;
    use TGetter;
    use TSetter;
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = [
        'asUT' => false,
        'format' => 'Y-m-d H:i:s',
        'humanize' => false,
        'natural' => true,
    ];
    /* Public */

    public function __construct($properties = [])
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }
}
