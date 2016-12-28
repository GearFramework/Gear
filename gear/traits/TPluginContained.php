<?php

namespace gear\traits;

/**
 * Трэйт для объектов, которым необходимо поддерживать плагины
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @license MIT
 * @since 23.08.2016
 * @version 1.0.0
 */
trait TPluginContained
{
    protected $_plugins = [];

    protected function _preloadPlugins($plugins)
    {
        foreach($plugins as $name => $plugin) {
            $this->installPlugin($name, $plugin, $this);
        }
    }

    public function p($name)
    {
        if (!($plugin = $this->isPluginInstalled($name))) {
            if (!($plugin = $this->isPluginRegistered($name))) {
                throw static::exceptionPluginNotAllowed(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
            }
            $plugin = $this->installPlugin($name, $plugin, $this);
        }
        return $plugin;
    }

    public function isPlugin($name)
    {
        if (!($plugin = isset($this->_plugins[$name]))) {
            $plugin = static::i('plugins');
            $plugin = isset($plugin[$name]) ? $this->installPlugin($name, $plugin[$name]) : false;
        }
        return $plugin;
    }

    public function isPluginRegistered($name)
    {
        if (isset(static::$_config['plugins'][$name]))
            $plugin = static::$_config['plugins'][$name];
        else if (isset(self::$_config['plugins'][$name]))
            $plugin = self::$_config['plugins'][$name];
        else
            $plugin = false;
        return $plugin;
    }

    public function registerPlugin($name, $plugin)
    {
        if ($plugin instanceof \Closure)
            $plugin = $plugin($name);
        if (!is_array($plugin))
            throw static::exceptionPluginRegisteringIsInvalid(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        static::$_config['plugins'][$name] = $plugin;
    }

    public function isPluginInstalled($name)
    {
        return isset($this->_plugins[$name]) ? $this->_plugins[$name] : false;
    }

    public function installPlugin($name, $plugin, $owner = null)
    {
        if (is_array($plugin)) {
            list($class, $config, $properties) = \gear\Core::configure($plugin);
            $plugin = $class::install($config, $properties, $owner === null ? $this : $owner);
        }
        if (!($plugin instanceof \gear\interfaces\IPlugin)) {
            throw static::exceptionPluginInstallationIsInvalid(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        }
        $this->_plugins[$name] = $plugin;
        return $plugin;
    }

    public function uninstallPlugin($name)
    {
        if (isset($this->_plugins[$name])) {
            $this->p($name)->uninstall();
            unset($this->_plugins[$name]);
        }
    }

    public function getPlugins()
    {
        return $this->_plugins;
    }
}