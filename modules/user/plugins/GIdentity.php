<?php

namespace gear\modules\user\plugins;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GException;
use \gear\interfaces\IIdentity;

/** 
 * Класс-обёртка плагина идентификации пользователя
 * 
 * @package Arquivo Corporation Edition
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 08.05.2013
 */
abstract class GIdentity extends GPlugin implements IIdentity
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    /* Public */
    
    /**
     * Вызов метода идентифкации
     * 
     * @access public
     * @return object
     */
    public function __invoke()
    {
        return call_user_func_array(array($this, 'identity'), func_get_args());
    }
    
    /**
     * Метод идентификации пользователя
     * 
     * @access public
     * @return object
     */
    public function identity()
    {
        $this->event('onBeforeIdentity');
        $result = $this->_identity();
        $this->event('onAfterIdentity', $result);
        return $result;
    }
    
    abstract protected function _identity();
}

/** 
 * Исключения плагина идентификации
 * 
 * @package Arquivo Corporation Edition
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 08.05.2013
 */
class IdentityException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}