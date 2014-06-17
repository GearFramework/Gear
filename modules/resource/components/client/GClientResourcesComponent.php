<?php

namespace gear\modules\resource\components\client;
use gear\Core;
use gear\modules\resource\library\GResourceComponent;

/** 
 * Компонент для работы с клиентскими ресурсами (javascript, css) 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 17.06.2014
 */
class GClientResourcesComponent extends GResourceComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected static $_config = array
    (
        'plugins' => array
        (
            'js' => array('class' => '\gear\modules\resource\plugins\GJsResource'),
            'css' => array('class' => '\gear\modules\resource\plugins\GCssResource'),
            'cache' => array('class' => '\gear\modules\resource\plugins\GCacheResource'),
        ),
    );
    /* Public */
    public $resourcesPath = 'resources';
    public $salt = 'Rui43VbthF#';
    public $useCache = false;
    
    /**
     * Публикация ресурса (ввиде ссылки). Параметр $render установленный в true
     * позволяет провести предварительный рендеринг ресурса в шаблонизаторе,
     * таким образом ресурс может быть динамическим и содержать php-код 
     * 
     * @access public
     * @param string $resource
     * @param boolean $render
     * @return string
     */
    public function publicate($resource, $render = false)
    {
        $ext = substr(strrchr($resource, '.'), 1);
        if (!$this->isPluginRegistered($ext))
            $this->e('Unknown resource ":resourceName"', array('resourceName' => $resource));
        return call_user_func_array(array($this->p($ext), 'publicate'), func_get_args());
    }
    
    /**
     * Возвращает контент ресурса
     * 
     * @access public
     * @param string $resource
     * @return mixed
     */
    public function get($resource)
    {
        $params = explode(':', $resource, 2);
        if (count($params) === 2)
        {
            list($handler, $hash) = $params;
            if ($this->isPluginRegistered($handler))
                return $this->p($handler)->get($hash);
        }
        return false;
    }
}
