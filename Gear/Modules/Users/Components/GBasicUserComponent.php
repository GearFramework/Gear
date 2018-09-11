<?php

namespace Gear\Modules\Users\Components;

use Gear\Library\Db\GDbStorageComponent;
use Gear\Library\GEvent;
use Gear\Modules\Users\GUserModule;
use Gear\Modules\Users\Interfaces\IUser;
use Gear\Modules\Users\Interfaces\IUserComponent;

/**
 * Базовый компонент для работы с пользователями
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 *
 * @property null|IUser user
 *
 * @since 0.0.1
 * @version 0.0.1
 */
class GBasicUserComponent extends GDbStorageComponent implements IUserComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected static $_config = [
        'plugins' => [
            'session' => [
                'class' => '\Gear\Modules\Users\Plugins\GSessionUserIdentity',
                'collectionName' => 'sessions',
                'connectionName' => 'db',
                'dbName' => 'simple',
            ],
        ],
    ];
    protected $_collectionName = 'users';
    protected $_connectionName = 'db';
    protected $_dbName = 'simple';
    protected $_factoryProperties = [
        'class' => '\Gear\Modules\Users\Models\GUser',
    ];
    protected $_guestUser = 'guest';
    protected $_identityPlugins = ['session'];
    protected $_user = null;
    /* Public */

    public function afterInstallService()
    {
        foreach ($this->_identityPlugins as $pluginName) {
            $this->p($pluginName);
        }
        return parent::afterInstallService();
    }

    /**
     * Возвращает текущий модуль для работы с пользователями
     *
     * @return GUserModule
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAuthModule(): GUserModule
    {
        return $this->owner;
    }

    /**
     * Возвращает текущего идентифицированного пользователя или NULL, если такового нет
     *
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUser(): ?IUser
    {
        return $this->_user;
    }

    /**
     * Идентификация пользователя
     *
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function identity(): ?IUser
    {
        /**
         * @var IUser $user
         */
        $user = null;
        foreach ($this->_identityPlugins as $pluginName) {
            $criteria = $this->p($pluginName)->identity();
            if ($criteria) {
                $user = $this->loadUser($criteria);
                if ($this->isValid($user)) {
                    $this->trigger('onUserIdentity', new GEvent($this, ['user' => $user]));
                    break;
                }
            }
        }
        return $user;
    }

    /**
     * Возвращает true, если пользователь является правильным зарегистрированным и аутентифицированным
     * Гостевой пользователь таковым не является
     *
     * @param IUser $user
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isValid(IUser $user): bool
    {
        if ($this->_guestUser) {
            if ($this->user instanceof IUser && $this->username !== $this->_guestUser) {
                return true;
            } else {
                return false;
            }
        } else {
            return $this->user instanceof IUser;
        }
    }

    /**
     * Загрзука пользователя из базы данных согласно указанному критерию
     *
     * @param array $criteria
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function loadUser(array $criteria): ?IUser
    {
        return $this->findOne($criteria);
    }

    /**
     * Авторизация пользователя
     *
     * @param array $criteria
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function login($criteria = []): ?IUser
    {
        if ($user = $this->loadUser($criteria)) {
            $this->trigger('onUserLogin', new GEvent($this, ['user' => $user]));
        }
        return $user;
    }

    /**
     * Снятие авторизации пользователя
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function logout()
    {
        $this->trigger('onUserLogout', new GEvent($this, ['user' => $this->user]));
    }
}
