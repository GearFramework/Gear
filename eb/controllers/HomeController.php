<?php

namespace eb\controllers;

use gear\library\GController;

class HomeController extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_viewPath = 'views/home';
    /* Public */
    public $caption = 'Главная страница';

    public function apiIndex()
    {
        $this->view->render('home');
    }
}