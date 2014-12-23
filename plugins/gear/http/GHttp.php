<?php

namespace gear\plugins\gear\http;
use gear\Core;
use gear\library\GPlugin;
use gear\library\GException;

class GHttp extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_header = array
    (
        'class' => 'gear\plugins\gear\http\GHeader',
    );
    /* Public */
    
    public function header()
    {
        $header = $this->getHeader();
        return !func_num_args() ? $header : call_user_func_array($header, func_get_args());
    }
    
    public function getHeader()
    {
        if (is_array($this->_header))
        {
            list($class, $properties) = Core::getRecords($this->_header);
            $this->_header = new $class($properties);
        }
        return $this->_header;
    }
    
    public function setHeader(array $header)
    {
        $this->_header = $header;
        return $this;
    }
    
    public function out($data, $buffering = false)
    {
        echo $data;
    }
}

class HttpException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
