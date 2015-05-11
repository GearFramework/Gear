<?php

namespace gear\components\gear\helper;
use gear\Core;
use gear\library\GComponent;
use gear\library\GException;

/**
 * Менеджер хелперов
 *
 * @package Gear Framework
 * @component Helper
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 21.04.2015
 */
class GHelperManager extends GComponent
{
    /* Const */
    /* Private */
    private $_helpers = null;
    private $_activeHelper = null;
    /* Protected */
    protected static $_config = [];
    protected static $_init = false;
    /* Public */

    public function registerHelpers(array $helpers)
    {
        $this->_helpers = $helpers;
        return $this;
    }

    public function runHelper($name)
    {
        if (!isset($this->_helpers[$name]))
            $this->e('Helper :helperName not found', ['helperName' => $name]);
        if ($this->_activeHelper !== $this->_helpers[$name])
        {
            if (!is_object($this->_helpers[$name]))
            {
                list($class, $config, $properties) = Core::getRecords($this->_helpers[$name]);
                $this->_helpers[$name] = $class::it($properties);
            }
            $this->_activeHelper = $this->_helpers[$name];
        }
        return $this;
    }

    /**
     * Возвращает true, если указанный хелпер зарегистрирован
     *
     * @access public
     * @param string $name
     * @return bool
     */
    public function isHelperRegistered($name) { return isset($this->_helpers[$name]); }

    public function onCalled($event, $name, $args)
    {
        return call_user_func_array([$this->_activeHelper, $name], $args);
    }
}
