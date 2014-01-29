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
    /* Public */
    public $resources = null;
    
    /**
     * Получение реалтного пути к файлу-ресурсу
     * 
     * @access public
     * @param string $file
     * @return void
     */
    public function resolvePath($file)
    {
        if ($file[0] !== '\\')
            $file = $this->getOwner()->storage . '\\' . ($this->resources ? $this->resources . '\\' : '') . $file;
        return Core::resolvePath($file);
    }
    
    /**
     * Публикация ресурса
     * 
     * @access public
     * @param string $file
     * @param boolean $render
     * @return string as url
     */
    public function publicate($file, $render = false)
    {
        $file = $this->resolvePath($file);
        $hash = $this->getHash($file);
        if (!$this->inCache($hash))
            $hash = $this->cache($file, array('render' => $render));
        return $hash ? '?e=gear/resource/get&resource=' . $hash . '&contentType=' . $this->resources : '';
    }
    
    /**
     * Получение содержимого ресурса
     * 
     * @access public
     * @param string $hash
     * @return string
     */
    public function get($hash)
    {
        if ($this->inCache($hash))
        {
            $resource = $this->getOwner()->cache->get($hash, true);
            if (is_array($resource))
            {
                return $resource['render'] 
                       ? $this->view->render($resource['resource'], array(), true)
                       : file_get_contents($resource['resource']);
            }
        }
        return null;
    }
    
    /**
     * Получение mime-тип ресурса
     * 
     * @abstract
     * @access public
     * @return void
     */
    abstract public function getContentType();
}