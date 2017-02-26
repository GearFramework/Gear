<?php

namespace gear\traits;

use gear\Core;

trait TDelegateFactory
{
    protected $_delegat = [
        'class' => '\gear\library\GDelegateFactoriableIterator',
    ];

    public function delegate(\Iterator $source)
    {
        if (is_array($this->delegat)) {
            list($class,, $properties) = Core::configure($this->delegat);
            $this->delegat = new $class(array_merge($properties, ['source' => $source]), $this);
        } else {
            $this->delegat->source = $source;
        }
        return $this->delegat;
    }

    public function getDelegat()
    {
        return $this->_delegat;
    }

    public function setDelegat($delegat)
    {
        $this->_delegat = $delegat;
    }
}