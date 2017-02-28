<?php

namespace eb\controllers\clients;

use gear\Core;
use gear\modules\user\controllers\UserController;

class AuthController extends UserController
{
    public function getModule()
    {
        return Core::m('clients');
    }
}