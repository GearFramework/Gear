<?php

namespace gear\modules\resource\library;
use gear\Core;
use gear\library\GComponent;
use gear\library\GException;

/** 
 * Базовый класс, для компонентов расширяющих фунционал модуля ресурсов 
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 17.06.2014
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
    
    /**
     * Генерация ключа для ресурса
     * 
     * @access public
     * @param string $file
     * @return string
     */
    public function getHash($file) { return md5($file . $this->salt); }
}

/** 
 * Исключения ресурсных компонентов 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 17.06.2014
 */
class ResourceComponentException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
