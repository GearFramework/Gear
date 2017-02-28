<?php

namespace gear\modules\user\components;

use gear\Core;
use gear\interfaces\IModel;
use gear\library\db\GDbStorageComponent;
use gear\modules\user\models\GSession;

class GUserSessionComponent extends GDbStorageComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_factory = [
        'class' => '\gear\modules\user\models\GSession',
    ];
    protected static $_initialized = false;
    protected $_sessionName = 'userSession';
    protected $_session = null;
    /* Public */

    public function getSession()
    {
        return $this->_session;
    }

    public function setSession($session)
    {
        $this->_session = $session;
    }

    public function onAfterInstallService()
    {
        if (($session = Core::app()->request->session->{$this->_sessionName})) {
            if ($session['hash']) {
                $session = $this->byPk($session['hash']);
            } else {
                $session = $this->startNewSession();
                $this->add($session);
            }
        }
    }

    public function startNewSession(): GSession
    {
        $session = $this->factory(['hash' => $this->createHash(), 'user' => 0, 'sessiontime' => date('Y-m-d H:i:s')]);
        $_SESSION[$this->_sessionName] = $session->props();
        return $session;
    }

    public function createHash(): string
    {
        return password_hash((string)(time() + microtime(true)) + random_bytes(128) , PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
