<?php

namespace gear\modules\user;
use \gear\Core;
use \gear\library\GModule;
use \gear\library\GException;

/** 
 * Модуль для работы с пользователями
 * 
 * @package Gear Framework
 * @module User
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 29.07.2013
 */
class GUser extends GModule
{
    /* Const */
    const ERR_ISBLOCK = 1;
    const ERR_GUESTNOTALLOWED = 2;
    const ERR_USERNOTIDENTITY = 3;
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'behaviors' => array('db' => '\\gear\\behaviors\\GDbBehavior'),
    );
    protected $_isValid = false;
    protected $_isGuest = false;
    /* Public */
    public $name = 'user';
    public $connectionName = 'db';
    public $dbName;
    public $collectionName;
    
    /**
     * Возвращает true, если пользователь идентифицирован
     * 
     * @access public
     * @return boolean
     */
    public function isValid() { return $this->_isValid; }
    
    /**
     * Идентификация пользователя
     * 
     * @access public
     * @return $this
     */
    public function identity()
    {
        if (!$this->_isValid)
        {
            $properties = $this->c('identity')->identity();
            if ($properties)
            {
                $this->props($properties);
                $this->event('onIdentity');
            }
            else
                $this->event('onInvalidIdentity');
        }
        return $this;
    }
    
    public function logout()
    {
        if ($this->_isValid)
        {
            $this->c('identity')->logout();
            $this->event('onLogout');
        }
    }
    
    public function byId($userId)
    {
        return $this->getConnection()->findOne(array('id' => (int)$userId));
    }
    
    public function byUsername($username)
    {
        return $this->getConnection()->findOne(array('username' => $username));
    }
    
    public function byPassword($username, $password)
    {
        return $this->getConnection()->findOne(array('username' => $username, 'password' => $password));
    }

    public function rule()
    {
        
    }
    
    /**
     * Обработчик события, возникающего после успешной идентификации 
     * пользователя
     * 
     * @access public
     * @return boolean
     */
    public function onIdentity() { return $this->_isValid = true; }
}

/** 
 * Исключения модуля
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 29.07.2013
 */
class UserException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
