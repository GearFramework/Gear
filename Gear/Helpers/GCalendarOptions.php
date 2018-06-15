<?php

namespace Gear\Helpers;

use Gear\Traits\TGetter;
use Gear\Traits\TProperties;
use Gear\Traits\TSetter;

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
        'format' => 'Y-m-d H:i:s',
        'asUT' => false,
    ];
    /* Public */

    public function __construct($properties = [])
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }
}
