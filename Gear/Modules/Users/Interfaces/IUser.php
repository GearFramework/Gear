<?php

namespace Gear\Modules\Users\Interfaces;

/**
 * Интерфейс модели пользовательской сессии
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface ISession {}

/**
 * Интерфейс модели пользователя
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IUser{}

/**
 * Интерфейс компонента управляющего пользователями
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IUserComponent
{

    /**
     * Проверка и подтверждение регистрации нового пользователя
     *
     * @param array $arguments
     * @return IUser
     * @since 0.0.1
     * @version 0.0.1
     */
    public function confirmRegistered(...$arguments): IUser;

    /**
     * Возвращает текущего идентифицированного пользователя или NULL, если такового нет
     *
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUser(): ?IUser;

    /**
     * Идентификация пользователя
     *
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function identity(): ?IUser;

    /**
     * Возвращает true, если пользователь является правильным зарегистрированным и аутентифицированным
     * Гостевой пользователь таковым не является
     *
     * @param IUser $user
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isValid(IUser $user): bool;

    /**
     * Авторизация пользователя
     *
     * @return IUser|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function login(): ?IUser;

    /**
     * Снятие авторизации пользователя
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function logout();

    /**
     * Регистрация нового пользователя
     *
     * @param array $properties
     * @return IUser
     * @since 0.0.1
     * @version 0.0.1
     */
    public function register(array $properties): IUser;
}

/**
 * Плагин для идентификации пользователя
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IUserIdentityPlugin
{
    /**
     * Идентификация пользователя
     *
     * @return array|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function identity(): ?array;
}
