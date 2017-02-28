<?php

namespace gear\modules\user\controllers;

use gear\Core;
use gear\library\GController;
use gear\traits\TView;

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
    use TView;
    /* Const */
    /* Private */
    /* Protected */
    protected $_defaultApiName = 'auth';
    protected $_viewPath = '\gear\modules\user\views';
    /* Public */

    public function apiAuth()
    {
        echo $this->render('loginForm');
    }

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
            $this->getModule()->login(['username' => $username, 'password' => $password]);
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
            $this->getModule()->logout();
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
            $this->getModule()->register($this->request->post());
        } catch(\Exception $e) {

        }
    }

    public function getModule()
    {
        return Core::user();
    }
}