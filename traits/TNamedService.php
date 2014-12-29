<?php

namespace gear\traits;

trait TNamedService
{
    public function getName()
    {
        return $this->_name;
    }

    public function setName($nameService)
    {
        $this->_name = $nameService;
        return $this;
    }
    
    public function name()
    {
        return $this->getNameService();
    }
}