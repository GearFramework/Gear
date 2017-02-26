<?php

namespace eb\library;

use gear\Core;
use gear\library\GController;

abstract class EbController extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_access = Core::ACCESS_PROTECTED;
    /* Public */

    public function onAfterExecController($event)
    {
        if ($this->access != Core::ACCESS_PUBLIC && !Core::user()->identity()) {
            return Core::app()->redirect('operator/auth', ['back' => base64_encode(Core::app()->uri)]);
        }
    }
}
