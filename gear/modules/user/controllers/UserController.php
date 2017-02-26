<?php

namespace gear\modules\resources\controllers;

use gear\Core;
use gear\library\GController;

/**
 * Контроллер менеджера пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class UserController extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Аутентификация пользователя
     *
     * @param string $username
     * @param string $password
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function apiLogin(string $username, string $password)
    {
        try {
            Core::user()->login(['username' => $username, 'password' => $password]);
        } catch(\Exception $e) {

        }
    }

    /**
     * Выход пользователя
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function apiLogout()
    {
        try {
            Core::user()->logout();
        } catch(\Exception $e) {

        }
    }

    /**
     * Регистрация нового пользователя
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function apiRegister()
    {
        try {
            Core::user()->register($this->request->post());
        } catch(\Exception $e) {

        }
    }
}