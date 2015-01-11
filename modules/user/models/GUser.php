<?php

namespace gear\modules\user\models;

use gear\Core;
use gear\library\GModel;
use gear\library\GEvent;
use gear\library\GException;

/**
 * Модель - пользователь
 *
 * @package Gear Framework
 * @module User
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 29.07.2013
 */
class GUser extends GModel
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_isValid = false;
    protected $_isGuest = true;
    /* Public */

    /**
     * Возвращает true, если пользователь является гостем
     *
     * @access public
     * @return boolean
     */
    public function isGuest() { return $this->_isGuest; }

    /**
     * Возвращает true, если пользователь идентифицирован
     *
     * @access public
     * @return boolean
     */
    public function isValid() { return $this->_isValid && !$this->isGuest(); }

    /**
     * Идентификация пользователя
     *
     * @access public
     * @return $this
     */
    public function identity()
    {
        return $this->c('identity');
    }

    public function login()
    {
        if (!$this->_isValid)
        {
            $properties = $this->identity();
            if ($properties)
                $this->event('onUserIdentified', new GEvent($this, ['userProperties' => $properties]));
            else
                $this->event('onInvalidUserIdentified');
        }
        return $this;
    }

    public function logout()
    {
        if ($this->_isValid)
        {
            $this->owner->identity()->logout();
            $this->event('onLogout');
        }
    }

    /**
     * Обработчик события, возникающего после успешной идентификации
     * пользователя
     *
     * @access public
     * @param object $event
     * @return boolean
     */
    public function onUserIdentified($event) { $this->_isValid = true; }
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
