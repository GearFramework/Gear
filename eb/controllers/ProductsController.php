<?php

namespace eb\controllers;

use gear\Core;
use gear\library\GController;

class ProductsController extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_viewPath = 'views/products';
    /* Public */
    public $caption = 'Продукты';

    public function apiIndex()
    {
        $this->view->render('index');
    }
    
    public function apiGetCategories() 
    {
        
    }
}