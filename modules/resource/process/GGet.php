<?php

namespace gear\modules\resource\process;
use \gear\Core;
use \gear\library\GEvent;
use \gear\library\GException;
use \gear\models\GProcess;

class GGet extends GProcess
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public function apiIndex($resource, $contentType)
    {
        header('Content-Type: ' . Core::m('resources')->getContentType($contentType));
        echo Core::m('resources')->get($resource, $contentType);
    }
}
