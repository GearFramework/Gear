<?php

namespace gear\modules\resource\library;

use gear\library\GComponent;

/** 
 * Базовый класс, для компонентов расширяющих фунционал модуля ресурсов 
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 17.06.2014
 * @php 5.4.x or higher
 * @release 1.0.0
 */
abstract class GResourceComponent extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    /* Public */
    public $salt = '';
    
    /**
     * Публикация ресурса (ввиде ссылки)
     * 
     * @access public
     * @param string $resource
     * @return void
     */
    abstract public function publicate($resource);
    
    /**
     * Возвращает контент ресурса
     * 
     * @access public
     * @param string $hash
     * @return string
     */
    abstract public function get($hash);
}
