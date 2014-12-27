<?php

namespace gear\library\configurator;

use gear\Core;

/** 
 * Конфигуратор
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 27.12.2014
 */
class GConfigurator
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    /**
     * Обработка конфигурации
     * 
     * @access public
     * @param array $config
     * @return array
     */
    public function configure(array $config)
    {
        $this->_includeRecords($config);
        $this->_importRecords($config);
        return $config;
    }

    /**
     * Обработка импортируемых параметров
     * 
     * @access public
     * @param array $array
     * @return void
     */
    private function _importRecords(array &$array)
    {
        if (isset($array['#import']))
        {
            $import = $array['#import'];
            unset($array['#import']);
            if (is_array($import))
            {
                foreach($import as $importName)
                    $array[$importName] = Core::params($importName);
            }
            else
                $array[$import] = Core::params($import);
        }
    }
    
    /**
     * Обработка подключаемых параметров
     * 
     * @access public
     * @param array $array
     * @return void
     */
    private function _includeRecords(array &$array)
    {
        if (isset($array['#include']))
        {
            $include = require(Core::resolvePath($array['#include']));
            unset($array['#include']);
            if (is_array($include))
                $array = array_replace_recursive($array, $include);
        }
    }
}
