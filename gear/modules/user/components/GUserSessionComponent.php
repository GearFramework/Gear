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
    protected static $_defaultProperties = [
        'autoStartSession' => true,
    ];
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

    public function getSessionName()
    {
        return $this->_sessionName;
    }

    public function getValidSession()
    {
        return $this->session;
    }

    public function onAfterInstallService()
    {
        if ($this->autoStartSession) {
            $this->runSession();
        }
        return true;
    }

    public function remove($session)
    {
        Core::syslog(Core::INFO, 'Remove session <{sessionName}> from $_SESSION and database', ['sessionName' => $this->_sessionName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
        unset($_SESSION[$this->_sessionName]);
        return parent::remove($session);
    }

    public function runSession()
    {
        Core::syslog(Core::INFO, 'Check exists valid session <{sessionName}> in $_SESSION', ['sessionName' => $this->_sessionName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
        if (($session = Core::app()->request->session($this->sessionName))) {
            Core::syslog(Core::INFO, 'Session <{sessionName}> exists, check hash <{hash}> in database', ['hash' => $session['hash']?? '', 'sessionName' => $this->_sessionName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
            if ($session['hash'] && ($session = $this->byPk($session['hash']))) {
                try {
                    Core::syslog(Core::INFO, 'Session <{sessionName}> found by hash <{hash}>, validate...', ['hash' => $session->hash, 'sessionName' => $this->_sessionName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
                    $session->validate();
                    $session->update();
                } catch(\SessionExpiredException $e) {
                    Core::syslog(Core::WARNING, 'Session <{hash}> has expired, remove', ['hash' => $session->hash, '__func__' => __METHOD__, '__line__' => __LINE__], true);
                    $this->remove($session);
                    $session = $this->startNewSession();
                    Core::syslog(Core::INFO, 'New session <{hash}>', ['hash' => $session->hash, '__func__' => __METHOD__, '__line__' => __LINE__], true);
                } catch(\SessionInvalidTokenException $e) {
                    Core::syslog(Core::WARNING, 'Session <{hash}> invalid token <{token}>, remove', ['token' => $session->token, 'hash' => $session->hash, '__func__' => __METHOD__, '__line__' => __LINE__], true);
                    $this->remove($session);
                    throw self::HttpBadRequestException();
                }
            } else {
                Core::syslog(Core::WARNING, 'Session <{sessionName}> not found by hash <{hash}>, start new session', ['hash' => $session['hash']?? '', 'sessionName' => $this->_sessionName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
                $session = $this->startNewSession();
            }
        } else {
            Core::syslog(Core::WARNING, 'Session <{sessionName}> not exists, start new session', ['sessionName' => $this->_sessionName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
            $session = $this->startNewSession();
        }
        $this->session = $session;
    }

    public function setSession($session)
    {
        $this->_session = $session;
    }

    public function setSessionName($sessionName)
    {
        $this->_sessionName = $sessionName;
    }

    public function startNewSession(): GSession
    {
        Core::syslog(Core::INFO, 'Start new session <{sessionName}>', ['sessionName' => $this->sessionName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
        $session = $this->factory([
            'hash' => $this->createHash(),
            'token' => $this->createHash(),
            'user' => 0,
            'timeSession' => date('Y-m-d H:i:s')
        ]);
        Core::syslog(Core::INFO, 'New session hash <{hash}>', ['hash' => $session->hash, '__func__' => __METHOD__, '__line__' => __LINE__], true);
        Core::app()->request->session($this->sessionName, $session->props());
        $this->add($session);
        return $session;
    }


    public function update($session)
    {
        Core::syslog(Core::INFO, 'Update session by hash <{hash}>', ['hash' => $session->hash, '__func__' => __METHOD__, '__line__' => __LINE__], true);
        Core::app()->request->session($this->sessionName, $session->props());
        return parent::update($session);
    }
}
