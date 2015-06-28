<?php

namespace gear\models;

use gear\Core;
use gear\library\GModel;

/** 
 * Api-метод
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 18.12.2013
 * @php 5.3.x
 */
abstract class GApi extends GModel
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    /**
     * Получение процесса из-под которого выполняется api-метод
     * 
     * @access public
     * @return object
     */
    public function getProcess() { return $this->getOwner(); }
    
    /**
     * Исполнение api-метода
     *
     * @abstract
     * @access public
     * @return mixed
     */
    abstract public function runApi();
}