<?php

namespace gear\interfaces;

/** 
 * Интерфейс процесса
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 10.02.2014
 * @php 5.3.x
 * @release 1.0.0
 */
interface IProcessManager
{
    /**
     * Точка входа в процесс
     * 
     * @access public
     * @return mixed
     */
    public function entry();
}
