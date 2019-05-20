<?php

namespace Gear\Modules\Users\Components;

use Gear\Library\Db\GDbStorageComponent;
use Gear\Library\GEvent;
use Gear\Modules\Users\GUserModule;
use Gear\Modules\Users\Interfaces\UserComponentInterface;
use Gear\Modules\Users\Interfaces\UserInterface;
use Gear\Traits\Db\Mysql\DbStorageTrait;

/**
 * Базовый компонент для работы с пользователями
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 *
 * @property string collectionName
 * @property int confirmRegistered
 * @property string connectionName
 * @property string dbName
 * @property array factoryProperties
 * @property null|string guestUsername
 * @property array of strings identityPlugins
 * @property GUserModule owner
 * @property null|UserInterface user
 *
 * @since 0.0.1
 * @version 0.0.1
 */
class GBasicUserComponent extends GDbStorageComponent implements UserComponentInterface
{
    /* Traits */
    use DbStorageTrait;
    /* Const */
    const NO_CONFIRM = 0;
    const CONFIRM_EMAIL = 1;
    const CONFIRM_SMS = 2;
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
    protected $_confirmRegistered = self::NO_CONFIRM;
    protected $_connectionName = 'db';
    protected $_dbName = 'simple';
    protected $_factoryProperties = [
        'class' => '\Gear\Modules\Users\Models\GUser',
    ];
    protected $_guestUsername = 'guest';
    protected $_identityPlugins = ['session'];
    protected $_user = null;
    /* Public */

    /**
     * Вызывается после установки модуля.
     * Устанавливает плагины аутентификации
     *
     * @return mixed
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterInstallService()
    {
        foreach ($this->_identityPlugins as $pluginName) {
            $this->p($pluginName);
        }
        return parent::afterInstallService();
    }

    /**
     * Проверка и подтверждение регистрации нового пользователя
     *
     * @param array $arguments
     * @return UserInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function confirmRegistered(...$arguments): UserInterface
    {
        // TODO: Implement confirmRegistered() method.
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
     * Возвращает тип проверки регистрации пользователя
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConfirmRegistered(): int
    {
        return $this->_confirmRegistered;
    }

    /**
     * Возвращает логин гостевого пользователя
     *
     * @return null|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getGuestUsername(): ?string
    {
        return $this->_guestUsername;
    }

    /**
     * Возвращает список плагинов идентификации пользователей
     *
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIdentityPlugins(): iterable
    {
        return $this->_identityPlugins;
    }

    /**
     * Возвращает текущего идентифицированного пользователя или NULL, если такового нет
     *
     * @return null|UserInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUser(): ?UserInterface
    {
        return $this->_user;
    }

    /**
     * Идентификация пользователя
     *
     * @return null|UserInterface
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function identity(): ?UserInterface
    {
        /** @var UserInterface $user */
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
     * Возвращает true, если сервис требует проверки регистрации нового пользователя
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isRegistrationConfirm()
    {
        return (bool)$this->confirmRegistered;
    }

    /**
     * Возвращает true, если пользователь является правильным зарегистрированным и аутентифицированным
     * Гостевой пользователь таковым не является
     *
     * @param UserInterface $user
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isValid(UserInterface $user): bool
    {
        if ($this->_guestUser) {
            if ($this->user instanceof IUser && $this->username !== $this->_guestUser) {
                return true;
            } else {
                return false;
            }
        } else {
            return $this->user instanceof UserInterface;
        }
    }

    /**
     * Загрзука пользователя из базы данных согласно указанному критерию
     *
     * @param array $criteria
     * @return UserInterface|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function loadUser(array $criteria): ?UserInterface
    {
        return $this->findOne($criteria);
    }

    /**
     * Авторизация пользователя
     *
     * @param array $criteria
     * @return UserInterface|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function login($criteria = []): ?UserInterface
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

    /**
     * Регистрация нового пользователя
     *
     * @param array $properties
     * @return UserInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function register(array $properties): UserInterface
    {
        // TODO: Implement register() method.
    }

    /**
     * Установка типа проверки регистрации пользователя или отмена проверки
     *
     * @param int $confirm
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setConfirmRegistered(int $confirm)
    {
        $this->_confirmRegistered = $confirm;
    }

    /**
     * Установка логина для гостевого пользователя
     *
     * @param string $guestName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setGuestUsername(string $guestName)
    {
        $this->_guestUsername = $guestName;
    }

    /**
     * Установка списка плагинов идентификации пользователей
     *
     * @param iterable $plugins
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setIdentityPlugins(iterable $plugins)
    {
        $this->_identityPlugins = $plugins;
    }

    /**
     * Установка текущего идентифицированного пользователя или NULL, если такового нет
     *
     * @param UserInterface|null $user
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setUser(?UserInterface $user)
    {
        $this->_user = $user;
    }
}
