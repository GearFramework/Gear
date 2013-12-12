<?php

namespace gear\components\gear\access;
use \gear\Core;
use \gear\library\GModel;
use \gear\library\GException;

class GAccessElement extends GModel
{
    protected $_model = null;
    
    public function getModel()
    {
        return !$this->_model ? $this->_model = $this->getOwner()->byId($this->_properties['model']) : $this->_model;
    }
}