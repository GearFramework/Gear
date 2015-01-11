<?php

namespace gear\modules\user;

use gear\Core;
use gear\interfaces\IFactory;
use gear\library\GModule;
use gear\library\GEvent;
use gear\library\GException;

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
class GUserModule extends GModule implements IFactory
{
    /* Traits */
    use \gear\traits\TFactory;
    /* Const */
    const ERR_ISBLOCK = 1;
    const ERR_GUESTNOTALLOWED = 2;
    const ERR_USERNOTIDENTITY = 3;
    /* Private */
    /* Protected */
    protected static $_config = ['behaviors' => ['db' => '\\gear\\behaviors\\GDbBehavior']];
    protected static $_factoryItem = ['class' => '\gear\modules\user\models\GUser'];
    protected $_user = false;
    /* Public */
    public $name = 'user';
    public $connectionName = 'db';
    public $dbName;
    public $collectionName;

    /**
     * Возвращает пользователя
     *
     * @access public
     * @return object
     * @throws \gear\modules\user\UserModuleException
     */
    public function getUser()
    {
        if (!is_object($this->_user))
            $this->e('User is invalid');
        return $this->_user;
    }

    /**
     * Установка пользователя
     *
     * @access public
     * @param object $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * Возвращает true, если пользователь является гостем
     *
     * @access public
     * @return boolean
     */
    public function isGuest()
    {
        try { return $this->user->isGuest(); } catch(\Exception $e) { return true; }
    }

    /**
     * Возвращает true, если пользователь идентифицирован
     * 
     * @access public
     * @return boolean
     */
    public function isValid()
    {
        try { return $this->user->isValid(); } catch(\Exception $e) { return false; }
    }
    
    /**
     * Идентификация пользователя
     * 
     * @access public
     * @return $this
     */
    public function identity()
    {
        if (!$this->isValid())
        {
            $properties = $this->c('identity')->identity();
            if ($properties)
            {
                $this->event('onUserIdentified', new GEvent($this, ['userProperties' => $properties]));
            }
            else
                $this->event('onInvalidUserIdentified');
        }
        return $this->user;
    }
    
    public function logout()
    {
        if ($this->isValid())
        {
            $this->c('identity')->logout($this->user);
            $this->event('onUserLogout');
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
     * @param object $event
     * @return boolean
     */
    public function onUserIdentified($event)
    {
        $this->_user = $this->factory($event->userProperties);
        return Core::event('onUserIdentified', new GEvent($this->user));
    }

    /**
     * Обработчик события, возникающего после того как пользователь разлогинивается
     *
     * @access public
     * @return boolean
     */
    public function onUserLogout()
    {
        $this->user->logout();
        return Core::event('onUserLogout', new GEvent($this->user));
    }
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
class UserModuleException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
