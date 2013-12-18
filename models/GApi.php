<?php

namespace gear\models;

use \gear\Core;
use \gear\library\GModel;

abstract class GApi extends GModel
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public function getProcess() { return $this->getOwner(); }
    
    abstract public function runApi();
}