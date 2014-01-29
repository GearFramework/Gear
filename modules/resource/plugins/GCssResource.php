<?php

namespace gear\modules\resource\plugins;
use gear\Core;
use gear\modules\resource\plugins\GClientResource;

class GCssResource extends GClientResource
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $resources = 'css';
    
    public function getContentType()
    {
        return 'text/css';
    }
}