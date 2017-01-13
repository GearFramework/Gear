<?php

namespace gear\library\io\filesystem;

use gear\traits\TGetter;
use gear\traits\TProperties;
use gear\traits\TSetter;

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
