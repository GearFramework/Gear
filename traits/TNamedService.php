<?php

namespace gear\traits;

trait TNamedService
{
    public function getNameService()
    {
        return $this->_nameService;
    }

    public function setNameService($nameService)
    {
        $this->_nameService = $nameService;
        return $this;
    }
    
    public function nameService()
    {
        return $this->getNameService();
    }
}