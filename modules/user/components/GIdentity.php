<?php

namespace gear\modules\user\components;
use \gear\Core;
use \gear\library\GDbComponent;
use \gear\library\GException;

/** 
 * Компонент аутентификация и авторизация
 * 
 * @package Gear Framework
 * @component Identity
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 06.08.2013
 */
class GIdentity extends GDbComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    protected static $_init = false;
    protected $_identityRoute = array();
    /* Public */
    
    /**
     * Идентификация пользователя
     * 
     * @access public
     * @return array
     */
    public function identity()
    {
        $properties = null;
        foreach($this->_identityRoute as $identity)
        {
            if ($properties = $this->p($identity)->identity())
                break;
        }
        return $properties;
    }
    
    public function logout()
    {
        foreach($this->_identityRoute as $identity)
            $this->p($identity)->logout();
    }
    
    /**
     * Установка последовательности вызова плагинов, которые должны
     * идентифицировать пользователя
     * 
     * @access public
     * @param array $value
     * @return void
     */
    public function setIdentityRoute(array $value)
    {
        $this->_identityRoute = $value;
    }
    
    /**
     * Получение последовательности вызова плагинов, которые должны
     * идентифицировать пользователя
     * 
     * @access public
     * @return array
     */
    public function getIdentityRoute()
    {
        return $this->_identityRoute;
    }
}

/** 
 * Исключения компонент аутентификации и авторизации
 * 
 * @package Gear Framework
 * @component Identity
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 06.08.2013
 */
class IdentityException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */    
}
