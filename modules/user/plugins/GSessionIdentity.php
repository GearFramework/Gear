<?php

namespace gear\modules\user\plugins;
use \gear\Core;
use \gear\library\GException;
use \gear\library\GDbPlugin;
use \gear\interfaces\IIdentity;

/** 
 * Плагин идентифкации пользователя через cookie или php-сессию
 * 
 * @package Gear Framework
 * @plugin SessionIdentity
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 06.08.2013
 */
class GSessionIdentity extends GDbPlugin implements IIdentity
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'sessionName' => 'session',
        'cookie' => array('session', 604800),
    );
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
        $this->event('onAfterIdentity', new \gear\library\GEvent($this), $result);
        return $result;
    }

    /**
     * Идентификация пользователя по cookie И php-сессию
     * 
     * @access protected
     * @return array
     */
    protected function _identity()
    {
        $hash = null;
        $properties = null;
        if (!($hash = Core::app()->request->session($this->i('sessionName'))))
        {
            $this->log('Session hash not found');
            if ($cookie = $this->i('cookie'))
            {
                list($sessionName, $cookieLifeTime) = $cookie;
                $this->log('Identity by cookie...');
                $hash = Core::app()->request->cookie($sessionName);
            }
        }
        if ($hash)
        {
            $this->log('Hash ' . $hash);
            $session = $this->getConnection()->findOne(array('hash' => $hash));
            if ($session)
            {
                $this->props($session);
                $properties = Core::m('user')->byId($this->user);
            }
        }
        else
            $this->log('Cookie hash not found');
        return $properties;
    }
    
    public function logout()
    {
        $this->getConnection()->remove(array('hash' => $this->hash));
        if ($cookie = $this->i('cookie'))
        {
            list($sessionName, $cookieLifeTime) = $cookie;
            Core::app()->request->cookie($sessionName, $this->hash, time() - 3600);
            unset($_SESSION[$this->i('sessionName')]);
        }
    }
    
    /**
     * Создание сессии пользователя
     * 
     * @access public
     * @return $this
     */
    public function createSession()
    {
        $sessionName = $this->i('sessionName');
        $this->hash = md5((time() + microtime(true)) . rand(1, 100000));
        $this->log('Create session [hash=:sessionHash]', array('sessionHash' => $this->hash));
        Core::app()->request->session($sessionName, $this->hash);
        if ($cookie = $this->i('cookie'))
        {
            list($sessionName, $cookieLifeTime) = $cookie;
            Core::app()->request->cookie($sessionName, $this->hash, time() + $cookieLifeTime);
        }
        return $this;
    }
    
    /**
     * Сохранение сессии
     * 
     * @access public
     * @return $this
     */
    public function saveSession()
    {
        $hash = $this->props('hash');
        if (!$hash)
        {
            $this->createSession();
            $this->user = Core::m('user')->id;
            $this->lastupdate = date('Y-m-d H:i:s');
            $this->getConnection()->insert(array
            (
                'hash' => $this->hash,
                'user' => $this->user,
                'lastupdate' => $this->lastupdate,
            ));
        }
        else
        {
            $this->lastupdate = date('Y-m-d H:i:s');
            $this->getConnection()->update(array('hash' => $this->hash), array
            (
                'user' => Core::m('user')->id,
                'lastupdate' => $this->lastupdate,
            ));
            $this->updateSession();
        }
        return $this;
    }
    
    public function removeSession()
    {
        
    }
    
    /**
     * Обновление данных сессии
     * 
     * @access public
     * @return $this
     */
    public function updateSession()
    {
        $sessionName = $this->i('sessionName');
        Core::app()->request->session($sessionName, $this->hash);
        if ($cookie = $this->i('cookie'))
        {
            list($sessionName, $cookieLifeTime) = $cookie;
            Core::app()->request->cookie($sessionName, $this->hash, time() + $cookieLifeTime);
        }
        return $this;
    }
    
    /**
     * Обработчик события, возникающего после установки плагина SessionIdentity
     * 
     * @access public
     * @return boolean
     */
    public function onInstalled()
    {
        Core::m('user')->attachEvent('onIdentity', array($this, 'saveSession'));
        Core::m('user')->attachEvent('onLogout', array($this, 'removeSession'));
        return true;
    }
    
    /**
     * Обработчик события, возникающиего перед процедурой идентификации
     * пользователя через сессию
     * 
     * @access public
     * @return boolean
     */
    public function onBeforeIdentity()
    {
        $this->log('User identity by session...');
        return true;
    }
}

/** 
 * Исключения плагина идентифкации пользователя через cookie или php-сессию
 * 
 * @package Gear Framework
 * @plugin SessionIdentity
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 05.08.2013
 */
class SessionException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
