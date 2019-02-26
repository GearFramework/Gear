<?php

namespace Demo\Controllers;

use Gear\Library\GController;

/**
 * Контроллер главной страницы
 *
 * @package Demo Gear Framework
 * @author Kukushkin Denis
 * @copyright 2018 Kukushkin Denis
 * @since 0.0.1
 * @version 0.0.1
 */
class Home extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_layout = 'Views/Layouts/Page';
    protected $_title = 'Главная страница';
    protected $_viewPath = 'Views/Home';
    /* Public */

    public function apiIndex()
    {
        $this->render('Home');
    }
}