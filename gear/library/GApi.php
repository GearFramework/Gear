<?php

namespace gear\library;

use gear\library\GModel;

/** 
 * Api-метод
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 18.12.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
abstract class GApi extends GModel
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Вызов метода runApi
     *
     * @access public
     * @return mixed
     */
    public function __invoke() { return call_user_func_array(array($this, 'entry'), func_get_args()); }
    
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
    abstract public function entry();
}
