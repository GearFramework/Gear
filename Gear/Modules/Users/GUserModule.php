<?php

namespace Gear\Modules\Users;

use Gear\Components\Router\GRouterComponent;
use Gear\Core;
use Gear\Interfaces\ModelInterface;
use Gear\Library\GModel;
use Gear\Library\GModule;
use Gear\Modules\Users\Interfaces\UserComponentInterface;
use Gear\Modules\Users\Interfaces\UserInterface;

/**
 * Модуль для работы с аутентификацией и авторизацией пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 *
 * @property array|ModelInterface redirectRoutes
 * @property array routes
 * @property UserComponentInterface userComponent
 * @property string userComponentName
 *
 * @since 0.0.1
 * @version 0.0.1
 */
class GUserModule extends GModule
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static array $_config = [
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
    ];
    protected $_redirectRoutes = [
        'afterLogin' => 'home',
        'afterLogout' => 'home',
        'afterInvalidLogin' => 'auth',
        'afterInvalidIdentity' => 'auth',
        'auth' => 'auth',
        'login' => 'login',
        'logout' => 'logout',
    ];
    protected $_routes = [
        'auth' => '\Gear\Modules\Users\Controllers\Auth',
        'login' => '\Gear\Modules\Users\Controllers\Login',
        'logout' => '\Gear\Modules\Users\Controllers\Logout',
    ];
    protected $_userComponentName = 'basicUser';
    /* Public */

    /**
     * Проверка на существование вызванного метода у компонента, работающего
     * с пользователями
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \ComponentNotFoundException
     * @throws \CoreException
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __call(string $name, array $arguments)
    {
        /** @var UserComponentInterface $component */
        $component = $this->getUserComponent();
        if (method_exists($component, $name)) {
            return $component->$name(...$arguments);
        } else {
            return parent::__call($name, $arguments);
        }
    }

    /**
     * Вызывается после установки модуля.
     * Устанавливает свои роуты на контроллеры аутентификации пользователей
     *
     * @return mixed
     * @throws \ComponentNotFoundException
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterInstallService()
    {
        /**
         * @var $router GRouterComponent
         */
        $router = Core::app()->c(Core::props('routerName'));
        $router->addRoutes($this->routes);
        return parent::afterInstallService();
    }

    /**
     * Возвращает хэш-пароля
     *
     * @param string $passwordPlain
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function createPasswordHash(string $passwordPlain): string
    {
        return password_hash($passwordPlain, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Возвращает список редиректов
     *
     * @return ModelInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRedirectRoutes(): ModelInterface
    {
        if (is_array($this->_redirectRoutes)) {
            $this->_redirectRoutes = new GModel($this->_redirectRoutes);
        }
        return $this->_redirectRoutes;
    }

    /**
     * Возвращает роут по его названию
     *
     * @param string $name
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRoute(string $name): string
    {
        return $this->route($name);
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

    /**
     * Возвращает текущего аутентифицированного пользователя или NULL, если
     * такового нет
     *
     * @return null|UserInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getUser(): ?UserInterface
    {
        $this->userComponent->user;
    }

    /**
     * Возвращает компонент, отвечающий за работу с пользователями
     *
     * @return UserComponentInterface
     * @throws \ComponentNotFoundException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUserComponent(): UserComponentInterface
    {
        return $this->c($this->userComponentName);
    }

    /**
     * Возвращает название компонента, отвечающего за работу с пользователями
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUserComponentName(): string
    {
        return $this->_userComponentName;
    }

    /**
     * Идентификация пользователя
     *
     * @param mixed ...$arguments
     * @return null|UserInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function identity(...$arguments): ?UserInterface
    {
        return $this->userComponent->identity(...$arguments);
    }

    /**
     * Возвращает true, если пользователь является зарегистрированным
     *
     * @param UserInterface $user
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isValid(UserInterface $user): bool
    {
        return $this->userComponent->isValid($user);
    }

    /**
     * Возвращает роут по его названию
     *
     * @param string|null $name
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function route(string $name): ?string
    {
        return $this->redirectRoutes->$name;
    }

    /**
     * Установка списка редиректов
     *
     * @param iterable $routes
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setRedirectRoutes(iterable $routes)
    {
        $this->_redirectRoutes = $routes;
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

    /**
     * Установка названия компонента, отвечающего за работу с пользователями
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setUserComponentName(string $name)
    {
        $this->_userComponentName = $name;
    }

    /**
     * Проверка праролей на идентичность
     *
     * @param $password
     * @param $passwordUser
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function verifyPassword($password, $passwordUser): bool
    {
        return password_verify($password, $passwordUser);
    }
}
