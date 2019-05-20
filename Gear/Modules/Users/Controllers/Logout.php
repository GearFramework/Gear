<?php

namespace Gear\Modules\Users\Controllers;

use Gear\Core;
use Gear\Library\GController;

class Logout extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    public function apiIndex()
    {
        $user = Core::users()->indentity();
        if ($user) {
            Core::users()->logout($user);
            Core::app()->redirect(Core::users()->redirectAfterLogout);
        } else {
            Core::app()->redirect(Core::users()->redirectAfterLogout);
        }
    }
}