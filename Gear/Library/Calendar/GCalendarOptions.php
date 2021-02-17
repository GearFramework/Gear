<?php

namespace Gear\Library\Calendar;

use Gear\Traits\GetterTrait;
use Gear\Traits\PropertiesTrait;
use Gear\Traits\SetterTrait;

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
    use PropertiesTrait;
    use GetterTrait;
    use SetterTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected array $_properties = [
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
