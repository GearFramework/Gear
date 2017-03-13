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

    public function onBeforeExecController($event)
    {
        if ($this->access != Core::ACCESS_PUBLIC && !$this->getModule()->identity()) {
            return Core::app()->redirect($this->getModule()->authController);
        }
        return true;
    }
}
