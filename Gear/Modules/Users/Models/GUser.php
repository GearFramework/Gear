<?php

namespace Gear\Modules\Users\Models;

use Gear\Library\GModel;
use Gear\Modules\Users\Interfaces\ISession;
use Gear\Modules\Users\Interfaces\IUser;

/**
 * Базовая модель пользователя
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GUser extends GModel implements IUser
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_session = null;
    /* Public */

    /**
     * Возвращает сессию пользователя
     *
     * @return ISession|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSession(): ?ISession
    {
        return $this->_session;
    }

    /**
     * Устанавливает пользовательскую сессию
     *
     * @param null|ISession $session
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setSession(?ISession $session)
    {
        if ($this->id !== $session->user) {
            throw self::InvalidUserSessionException('Session <{hash}> is invalid for user <{username}>', [
                'hash' => $session->hash,
                'username' => $this->username,
            ]);
        }
        $this->_session = $session;
    }
}
