<?php

namespace gear\modules\user;

use gear\Core;
use gear\library\GModule;
use gear\modules\resources\controllers\PublicateController;
use gear\modules\user\models\GUser;

/**
 * Менеджер пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GUserModule extends GModule
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'components' => [
            'userDb' => [
                'class' => '\gear\modules\user\components\GUserDbComponent',
                'connectionName' => 'db',
                'dbName' => '',
                'collectionName' => '',
            ],
            'session' => [
                'class' => '\gear\modules\user\components\GUserSessionComponent',
                'connectionName' => 'db',
                'dbName' => '',
                'collectionName' => '',
            ],
        ],
        'loginController' => ['user'],
    ];
    protected static $_initialized = false;
    protected $_useComponent = 'userDb';
    protected $_model = [
        'id' => ['default' => 0, 'type' => 'int', 'index' => 'primary', 'options' => 'autoincrement'],
        'username' => ['type' => 'varchar(255)', 'charset' => 'utf8_general_ci'],
        'password' => ['type' => 'varchar(255)', 'charset' => 'utf8_general_ci', 'setter' => 'setterPassword'],
        'email' => ['type' => 'varchar(255)', 'charset' => 'utf8_general_ci'],
    ];
    protected $_authProperties = ['username'];
    /* Public */

    public function changePassword(string $oldPassword = '', string $newPassword = '')
    {

    }

    public function confirmRegister($token)
    {

    }

    public function getModel(): array
    {
        return $this->_model;
    }

    public function getUseComponent(): string
    {
        return $this->_useComponent;
    }

    public function getUser()
    {
        return $this->c($this->useComponent)->user;
    }

    public function identity()
    {
        try {
            $session = $this->session->getSession();
        } catch(\Exception $e) {
            Core::c('exceptionHandler')->exception($e);
            die();
        }

    }

    public function login(array $authProperties): GUser
    {
        $criteria = [];
        $model = $this->model;
        foreach($this->_authProperties as $name) {
            if (!isset($authProperties[$name]) || !isset($model[$name])) {
                throw self::UserLoginPropertyFailedException('Needed valid property <{name}>', ['name' => $name]);
            }
            $value = isset($model[$name]['setter'])
                     ? $this->{$model[$name]['setter']}($authProperties[$name])
                     : $authProperties[$name];
            $criteria[$name] = "\"$value\"";
        }
        if (!($user = $this->c($this->useComponent)->findOne($criteria))) {
            throw self::UserLoginFailedException('User not found');
        }
        if (!password_verify($authProperties['password'], $user->password)) {
            throw self::UserLoginFailedException('Wrong password');
        }
        return $user;
    }

    public function logout()
    {

    }

    public function register(array $model): GUser
    {
        $tempModel = $model;
        $model = [];
        foreach($this->model as $name => $data) {
            if (isset($data['default']) && !isset($tempModel[$name])) {
                $model[$name] = $data['default'];
            } else if (!isset($data['default']) && !isset($tempModel[$name])) {
                throw self::exceptionInvalidModel('Invalid user model, need valid <{name}> property', ['name' => $name]);
            } else {
                if (isset($data['setter'])) {
                    $model[$name] = $this->{$data['setter']}($tempModel[$name]);
                } else {
                    $model[$name] = $tempModel[$name];
                }
            }
        }
        $this->user = $this->c($this->useComponent)->factory($model);
        $this->c($this->useComponent)->add($this->user);
        return $this->user;
    }

    public function rememberPassword()
    {

    }

    public function setterPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function setModel(array $model)
    {
        $this->_model = $model;
    }

    public function setUser(GUser $user)
    {
        $this->c($this->useComponent)->user = $user;
    }
}
