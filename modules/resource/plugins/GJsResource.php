<?php

namespace gear\modules\resource\plugins;
use gear\Core;
use gear\modules\resource\plugins\GClientResource;

/** 
 * Плагин для работы с javascript-ресурсами 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 28.01.2014
 */
class GJsResource extends GClientResource
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $resources = 'js';
    
    /**
     * Получение mime-тип ресурса
     * 
     * @abstract
     * @access public
     * @return string
     */
    public function getContentType()
    {
        return 'text/javascript';
    }
}