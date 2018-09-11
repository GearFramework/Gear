<?php

namespace Gear\Modules\Users;

use Gear\Components\Router\GRouterComponent;
use Gear\Core;
use Gear\Library\GModule;
use Gear\Modules\Users\Interfaces\IUser;
use Gear\Modules\Users\Interfaces\IUserComponent;

/**
 * Модуль для работы с аутентификацией и авторизацией пользователей
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
            'basicUser' => [
                'class' => [
                    'name' => '\Gear\Modules\Users\Components\GBasicUserComponent',
                ],
                'connectionName' => 'db',
                'dbName' => 'simple',
                'collectionName' => 'users',
            ],
        ],
        'routes' => [
        ],
    ];
    protected $_routes = [
        'auth' => '\Gear\Modules\Users\Controllers\Auth',
        'login' => '\Gear\Modules\Users\Controllers\Login',
        'logout' => '\Gear\Modules\Users\Controllers\Logout',
    ];
    protected $_redirectAfterLogin = 'home';
    protected $_redirectAfterLogout = 'home';
    protected $_userComponentName = 'basicUser';
    /* Public */

    public function afterInstallService()
    {
        /**
         * @var $router GRouterComponent
         */
        $router = Core::app()->c(Core::props('routerName'));
        $router->addRoutes($this->routes);
        return parent::afterInstallService();
    }

    public function getRedirectAfterLogin(): string
    {
        return $this->_redirectAfterLogin;
    }

    public function getRedirectAfterLogout(): string
    {
        return $this->_redirectAfterLogout;
    }

    /**
     * Возвращает список роутов к контроллерам модуля
     *
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRoutes(): iterable
    {
        return $this->_routes;
    }

    public function getUser(): ?IUser
    {
        $this->userComponent->user;
    }

    public function getUserComponent(): IUserComponent
    {
        return $this->c($this->userComponentName);
    }

    public function getUserComponentName(): string
    {
        return $this->_userComponentName;
    }

    public function identity(...$arguments): ?IUser
    {
        return $this->userComponent->identity(...$arguments);
    }

    public function isValid(IUser $user): bool
    {
        return $this->userComponent->isValid($user);
    }

    public function login(...$arguments): ?IUser
    {
        return $this->userComponent->login(...$arguments);
    }

    public function logout(IUser $user)
    {
        $this->userComponent->logout($user);
    }

    /**
     * Установка списка роутов к контроллерам модуля
     *
     * @param iterable $routes
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setRoutes(iterable $routes)
    {
        $this->_routes = $routes;
    }
}
