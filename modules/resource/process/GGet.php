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
     * Точка входа в процесс
     * 
     * @access public
     * @param array $request
     * @return mixed
     */
    public function entry($request = array())
    {
        if ($this->event('onBeforeExec', new GEvent($this), $request))
        {
            $this->request = $request;
            $apiName = Core::app()->request->get('f');
            if (!$apiName)
                $apiName = $this->defaultApi;
            $result = false;
            if (Core::m('resources')->isComponentRegistered($apiName) &&
                Core::m('resources')->c($apiName) instanceof \gear\modules\resource\library\GResourceComponent)
            {
                $result = Core::m('resources')->c($apiName)->get(Core::app()->request->get('hash'));
            }
            $this->event('onAfterExec', new GEvent($this), $result);
            return $result;
        }
        return false;
    }

    /**
     * Api-метод по-умолчанию
     * 
     * @access public
     * @param string $hash md5-hash
     * @return boolean
     */
    public function apiIndex($hash)
    {
        echo '';
        return true;
    }
}
