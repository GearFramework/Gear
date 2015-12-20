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
 * @version 1.0.0
 * @since 28.01.2014
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GJsResource extends GClientResource
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_mappingFolder = 'js';
    protected $_contentType = 'text/javascript';
    protected $_extension = 'js';
    /* Public */
    public $html = "<script type=\"%s\" src=\"%s\"></script>\n";
    public $url = '?e=gear/resource/get&f=client&hash=js:%s';
    public $path = '/js';
    public $temp = 'temp/resources/js';
    
    /**
     * Возвращает подготовленную html-строку для публикации на странице
     * 
     * @access private
     * @param string $hash
     * @return string
     */
    public function getHtml($hash, $url = null, $params = []) {
        return sprintf($this->html, $this->getContentType(), sprintf($url ? $url : $this->url, $hash));
    }
}
