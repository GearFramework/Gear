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
        'append' => false,
        'skip' => false,
        'recursive' => false,
        'format' => '%01d %s',
        'force' => '',
    ];
    /* Public */
}
