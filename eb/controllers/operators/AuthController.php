<?php

namespace eb\controllers\operators;

use gear\Core;
use gear\modules\user\controllers\UserController;

/**
 * Контроллер менеджера операторов магазина
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class AuthController extends UserController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_layout = 'views/operators/authPage';
    protected $_viewPath = 'views/operators/auth';
    protected $_caption = 'Вход для операторов сайта';
    /* Public */

    public function apiDenied()
    {
        die('Доступ закрыт');
    }

    /**
     * Возвращает текущий модуль управления операторами магазина
     *
     * @return \gear\interfaces\IModule
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getModule()
    {
        return Core::m('operators');
    }
}
