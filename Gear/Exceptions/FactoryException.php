<?php

class FactoryException extends \Gear\Library\GException {}

class FactoryInvalidItemPropertiesException extends FactoryException
{
    public $defaultMessage = 'Item properties must be a an array';
}
