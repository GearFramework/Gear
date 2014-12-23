<?php

namespace gear\plugins\gear\http;
use gear\Core;
use gear\library\GModel;
use gear\library\GException;

class GHeader extends GModel
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public function __invoke()
    {
        $num = func_num_args();
        $args = func_get_args();
        if (!$num)
            return $this;
        else
        if ($num == 1)
        {
            if (is_array($args[0]) && \gear\helpers\Arrays::isAssoc($args[0]))
                return $this->setHeaders($args);
            else
                return $this->get($args[0]);
        }
        else
            return $this->set($args[0], $args[1]);
    }
    
    public function __toString()
    {
        
    }
    
    public function setHeaders()
    {
        
    }
    
    public function set($name, $value)
    {
        header($name . ': ' . $value);
    }
    
    public function get($name)
    {
    }
}

class HeaderException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
