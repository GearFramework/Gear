<?php

namespace Gear\Modules\Users;

use Gear\Components\Log\GLogComponent;
use Gear\Components\Router\GRouterComponent;
use Gear\Core;
use Gear\Interfaces\IModel;
use Gear\Library\GModel;
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
 *
 * @property bool debug
 * @property GLogComponent log
 * @property array|IModel redirectRoutes
 * @property array routes
 * @property IUserComponent userComponent
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
            'log' => [
                'class' => [
                    'name' => '\Gear\Components\Log\GLogComponent',
                    'plugins' => [
                        'file' => [
                            'class' => '\Gear\Plugins\Log\GFileLogger',
                            'location' => '\Gear\Modules\Users\Logs\User.log',
                            'levels' => [
                                Core::ALERT,
                                Core::INFO,
                                Core::NOTICE,
                                Core::WARNING,
                                Core::ERROR,
                                Core::EXCEPTION,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
    protected $_debug = false;
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
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __call(string $name, array $arguments)
    {
        /**
         * @var IUserComponent $component
         */
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

    public function getDebug(): bool
    {
        return $this->_debug;
    }

    /**
     * Возвращает список редиректов
     *
     * @return IModel
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRedirectRoutes(): IModel
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
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUser(): ?IUser
    {
        $this->userComponent->user;
    }

    /**
     * Возвращает компонент, отвечающий за работу с пользователями
     *
     * @return IUserComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUserComponent(): IUserComponent
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
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function identity(...$arguments): ?IUser
    {
        if ($this->debug) {
            $this->log->info('Start user identity by <{component}>', ['component' => $this->userComponentName]);
        }
        return $this->userComponent->identity(...$arguments);
    }

    /**
     * Возвращает true, если пользователь является зарегистрированным
     *
     * @param IUser $user
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isValid(IUser $user): bool
    {
        if ($this->debug) {
            $this->log->info('Validate user');
        }
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

    public function setDebug(bool $debug)
    {
        $this->_debug = $debug;
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
     * Проверка праролей на соответствие
     *
     * @param $password
     * @param $passwordUser
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function verifyPassword($password, $passwordHash): bool
    {
        $result = password_verify($password, $passwordHash);
        if ($this->debug) {
            $this->log->info('Verifed password width hash <{result}>', ['result' => $result ? 'TRUE' : 'FALSE']);
        }
        return $result;
    }
}
