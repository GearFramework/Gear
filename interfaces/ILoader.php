<?php

namespace gear\interfaces;

/** 
 * Интерфейс автозагрузчика классов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 * @release 1.0.0
 */
interface ILoader
{
    /**
     * Получение физического пути.
     * 
     * @access public
     * @param string $namespace
     * @return string
     */
    public function resolvePath($namespace);
}
