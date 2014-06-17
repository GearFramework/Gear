<?php

namespace gear\modules\resource\plugins;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GEvent;
use \gear\library\GException;

/** 
 * Каркас для ресурсов типа javascript, css 
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 28.01.2014
 */
abstract class GClientResource extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_mappingFolder = '';
    /* Public */
    public $temp = 'temp/resources';
    
    public function getMappingFolder()
    {
        return $this->_mappingFolder;
    }
    
    public function setMappingFolder($folder)
    {
        $this->_mappingFolder = $folder;
        return $this;
    }
    
    /**
     * Публикация ресурса (ввиде ссылки). Параметр $render установленный в true
     * позволяет провести предварительный рендеринг ресурса в шаблонизаторе,
     * таким образом ресурс может быть динамическим и содержать php-код.
     * Параметр $mapping, установленный в true копирует скрипт в указанную 
     * папку в DOCUMENT_ROOT
     * 
     * @access public
     * @param string $resource
     * @param boolean $render
     * @param boolean $mapping
     * @return string
     */
    abstract public function publicate($file, $render = false, $mapping = false);
    
    /**
     * Возвращает контент ресурса
     * 
     * @access public
     * @param string $hash
     * @return string
     */
    abstract public function get($hash);
        
    /**
     * Получение mime-тип ресурса
     * 
     * @abstract
     * @access public
     * @return void
     */
    abstract public function getContentType();
}
