<?php

namespace gear\library;

use gear\Core;
use gear\library\GService;
use gear\library\TEvents;
use gear\library\TComponents;
use gear\library\TBehaviors;
use gear\library\TPlugins;
use gear\interfaces\IModule;

/** 
 * Класс модулей
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
abstract class GModule extends GService implements IModule
{
    /* Traits */
    use TEvents;
    use TComponents;
    use TBehaviors;
    use TPlugins;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'events' => [],
        'behaviors' => [],
        'plugins' => ['view' => ['class' => '\gear\plugins\gear\GView']],
    ];
    protected static $_init = false;
    /* Public */

    /**
     * Конфигурирование модуля
     *
     * @access public
     * @static
     * @param string|array $config
     * @return bool
     * @throws GException
     */
    public static function init($config)
    {
        parent::init($config);
        if (isset(static::$_config['components'])) {
            foreach(static::$_config['components'] as $componentName => $component)
                Core::services()->registerService(get_called_class() . '.components.' . $componentName, $component);
        }
        return static::$_init;
    }

    /**
     * Возвращает true, если модуль может быть перегружен, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isOverride()
    {
        return isset($this->_properties['override']) && (bool)$this->_properties['override'] === true;
    }
}
