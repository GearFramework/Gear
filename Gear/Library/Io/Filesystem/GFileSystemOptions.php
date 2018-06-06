<?php

namespace Gear\Library\Io\Filesystem;

use Gear\Traits\TGetter;
use Gear\Traits\TProperties;
use Gear\Traits\TSetter;

class GFileSystemOptions
{
    /* Traits */
    use TProperties;
    use TGetter;
    use TSetter;
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = [
        'overwrite' => false,
        'mode' => null,
        'permission' => null,
        'own' => null,
        'append' => false,
        'skip' => false,
        'recursive' => false,
        'format' => '%01d %s',
        'force' => '',
        'append' => false,
        'ignoreNewLines' => false,
        'group' => null,
        'user' => null,
    ];
    /* Public */
}
