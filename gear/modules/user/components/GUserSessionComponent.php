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

    public function createHash(): string
    {
        return password_hash((string)(time() + microtime(true)) . random_bytes(128) , PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function getSession()
    {
        return $this->_session;
    }

    public function onAfterInstallService()
    {
        if (($session = Core::app()->request->session->{$this->_sessionName})) {
            if ($session['hash'] && ($session = $this->byPk($session['hash']))) {
                try {
                    $session->validate();
                    $session->updateToken();
                    $session->update();
                } catch(\SessionExpiredException $e) {
                    $this->remove($session);
                    $session = $this->startNewSession();
                } catch(\SessionInvalidTokenException $e) {
                    $this->remove($session);
                    throw self::exceptionHttpBadRequest();
                }
            } else {
                $session = $this->startNewSession();
            }
        } else {
            $session = $this->startNewSession();
        }
        $this->session = $session;
        return true;
    }

    public function remove($session)
    {
        unset($_SESSION[$this->_sessionName]);
        return parent::remove($session);
    }

    public function setSession($session)
    {
        $this->_session = $session;
    }

    public function startNewSession(): GSession
    {
        $session = $this->factory([
            'hash' => $this->createHash(),
            'token' => $this->createHash(),
            'user' => 0,
            'timeSession' => date('Y-m-d H:i:s')
        ]);
        Core::app()->request->session($this->_sessionName, $session->props());
        $this->add($session);
        return $session;
    }


    public function update($session)
    {
        Core::app()->request->session($this->_sessionName, $session->props());
        return parent::add($session);
    }

    public function updateToken(GSession $session)
    {
        $session->token = $this->createHash();
    }
}
