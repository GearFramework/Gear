<?php

namespace gear\modules\user\plugins;
use \gear\Core;
use \gear\library\GException;
use \gear\modules\user\plugins\GIdentity;

/** 
 * Плагин идентификации пользователя через веб-форму ввода логина и пароля
 * 
 * @package Gear Framework
 * @plugin FormIdentity
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 06.08.2013
 */
class GFormIdentity extends GIdentity
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    /* Public */
    
    /**
     * Идентификация пользователя через веб-форму
     * 
     * @access protected
     * @return array
     */
    protected function _identity()
    {
        list($usernameForm, $passwordForm) = array_keys($this->form);
        return Core::m('user')->byPassword
        (
            Core::app()->request->request($usernameForm),
            md5(Core::app()->request->request($passwordForm))
        );
    }
    
    public function logout() { return true; }
    
    /**
     * Обработчик события, вызываемого перед процессом идентификации
     * 
     * @access public
     * @return boolean
     */
    public function onBeforeIdentity()
    {
        Core::app()->log->write('User identity by html form...');
        return true;
    }
}

/** 
 * Исключения плагина идентификации пользователя через веб-форму 
 * ввода логина и пароля
 * 
 * @package Gear Framework
 * @plugin SessionIdentity
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 06.08.2013
 */
class FormIdentityException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
