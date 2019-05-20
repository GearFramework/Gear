<?php

namespace Gear\Modules\Users\Models;

use Gear\Library\GModel;
use Gear\Modules\Users\Interfaces\SessionInterface;
use Gear\Modules\Users\Interfaces\UserInterface;

/**
 * Базовая модель пользователя
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 *
 * @property int id
 * @property SessionInterface session
 * @property string username
 *
 * @since 0.0.1
 * @version 0.0.1
 */
class GUser extends GModel implements UserInterface
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
     * @return null|SessionInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSession(): ?SessionInterface
    {
        return $this->_session;
    }

    /**
     * Устанавливает пользовательскую сессию
     *
     * @param null|SessionInterface $session
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setSession(?SessionInterface $session)
    {
        if ($session && $this->id !== $session->user) {
            throw self::InvalidUserSessionException('Session <{hash}> is invalid for user <{username}>', [
                'hash' => $session->hash,
                'username' => $this->username,
            ]);
        }
        $this->_session = $session;
    }
}
