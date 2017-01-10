<?php

namespace gear\library\io\filesystem;

use gear\traits\TGetter;
use gear\traits\TObject;
use gear\traits\TSetter;

class GFileSystemOptions
{
    /* Traits */
    use TObject;
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
    ];
    /* Public */
}
