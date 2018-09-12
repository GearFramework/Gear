<?php

namespace Gear\Modules\Users\Controllers;

use Gear\Library\GController;

class Auth extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_layout = 'Modules\Users\Views\AuthPage';
    protected $_title = 'User login';
    protected $_viewPath = 'Modules\Users\Views';
    /* Public */

    public function apiIndex()
    {
        $this->render('Form');
    }
}