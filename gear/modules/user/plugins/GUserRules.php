<?php

namespace gear\modules\user\plugins;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GException;

/** 
 * Плагин проверки доступа идентифицированного пользователя
 * 
 * @package Gear Framework
 * @component UserRules
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GUserRules extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_rules = array();
    /* Public */
    
    /**
     * Установка правил доступа
     * 
     * @access public
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules) { $this->_rules = $rules; }
    
    /**
     * Получение правил доступа
     * 
     * @access public
     * @return array
     */
    public function getRules() { return $this->_rules; }
    
    /**
     * Проверка доступа идентифицированного пользователя
     * 
     * @access public
     * @return void
     */
    public function checkRules()
    {
        
    }
    
    /**
     * Обработчик события, возникающего после установки плагина
     * 
     * @access public
     * @return boolean
     */
    public function onInstalled()
    {
        Core::m('user')->attachEvent('onIdentity', array($this, 'checkRules'));
        return true;
    }
}

/** 
 * Исключения плагина проверки доступа идентифицированного пользователя
 * 
 * @package Gear Framework
 * @component UserRules
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class UserRulesException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
