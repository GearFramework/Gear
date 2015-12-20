<?php

namespace gear\modules\resource;
use \gear\Core;
use \gear\library\GModule;
use \gear\library\GEvent;
use \gear\library\GException;

/** 
 * Модуль для работы с ресурсами 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 28.01.2014
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GResource extends GModule
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'components' => [
            'client' => [
                'class' => '\gear\modules\resource\components\client\GClientResourcesComponent',
                'salt' => 'Rui43VbthF#',
            ],
        ],
    ];
    /* Public */
    
    /**
     * Публикация ресурса 
     * 
     * @access public
     * @param string $component
     * @param string $resource
     * @return string
     */
    public function publicate($component, $resource) {
        if ($this->isComponentRegistered($component) && 
            $this->c($component) instanceof \gear\modules\resource\library\GResourceComponent) {
            $args = func_get_args();
            array_shift($args);
            return call_user_func_array([$this->c($component), 'publicate'], $args);
        }
        else
            throw $this->exceptionUnkownResource(['resourceName' => $resource]);
    }
    
    /**
     * Возвращает контент ресурса
     * 
     * @access public
     * @param string $component
     * @param string $hash
     * @return mixed
     */
    public function get($component, $hash) {
        if ($this->isComponentRegistered($component) && 
            $this->c($component) instanceof \gear\modules\resource\library\GResourceComponent) {
            $args = func_get_args();
            array_shift($args);
            return call_user_func_array([$this->c($component), 'get'], $args);
        }
        else
            throw $this->exceptionUnkownResource(['resourceName' => $hash]);
    }
}
