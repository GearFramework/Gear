<?php

namespace gear\modules\resource\process;
use \gear\Core;
use \gear\library\GEvent;
use \gear\library\GException;
use \gear\models\GProcess;

/** 
 * Процесс получения контента запрашиваемого ресурса 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 28.01.2014
 */
class GGet extends GProcess
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    /**
     * Api-метод отдаёт содержимое указанного ресура
     * 
     * @access public
     * @param string $resource md5-hash
     * @param string $contentType has wrapper (default js|css)
     * @return boolean
     */
    public function apiIndex($resource, $contentType)
    {
        header('Content-Type: ' . Core::m('resources')->getContentType($contentType));
        echo Core::m('resources')->get($resource, $contentType);
        return true;
    }
}
