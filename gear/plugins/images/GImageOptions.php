<?php

namespace gear\library\io\filesystem;

use gear\traits\TGetter;
use gear\traits\TProperties;
use gear\traits\TSetter;

class GImageOptions
{
    /* Traits */
    use TProperties;
    use TGetter;
    use TSetter;
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = [
        'destination' => null,
        'quality' => 80,
        'permission' => null,
        'group' => null,
        'user' => null,
        /* proportional | cover | crop */
        'resizeMode' => 'proportional',
    ];
    /* Public */
}
