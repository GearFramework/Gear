<?php

namespace gear\modules\resource\plugins;

use gear\Core;
use gear\modules\resource\plugins\GClientResource;
use gear\traits\TView;

/** 
 * Плагин для работы с css-ресурсами 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 28.01.2014
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GCssResource extends GClientResource
{
    /* Traits */
    use TView;
    /* Const */
    /* Private */
    /* Protected */
    protected $_mappingFolder = 'css';
    protected $_contentType = 'text/css';
    protected $_extension = 'js';
    /* Public */
    public $html = "<link href=\"%s\" rel=\"stylesheet\" type=\"text/css\" \>\n";
    public $url = '?e=gear/resource/get&f=client&hash=css:%s';
    public $path = 'css'; 
    public $temp = 'temp/resources/css';

    /**
     * Возвращает подготовленную html-строку для публикации на странице
     * 
     * @access private
     * @param string $hash
     * @return string
     */
    public function getHtml($hash, $url = null, $params = []) {
        return sprintf($this->html, sprintf($url ? $url : $this->url, $hash));
    }
}
