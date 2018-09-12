<?php

namespace Gear\Modules\Users\Controllers;

use Gear\Core;
use Gear\Library\GController;

class Login extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    public function apiIndex($username, $password)
    {
        $user = Core::users()->login(['username' => $username, 'password' => $password]);
        if ($user) {
            Core::app()->redirect(Core::users()->redirectAfterLogin);
        } else {
            Core::app()->redirect(Core::users()->loginRoute);
        }
    }
}