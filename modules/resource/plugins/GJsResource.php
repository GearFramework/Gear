<?php

namespace gear\modules\resource\plugins;
use gear\Core;
use gear\modules\resource\plugins\GClientResource;

class GJsResource extends GClientResource
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $resources = 'js';
    
    public function getContentType()
    {
        return 'text/javascript';
    }
}