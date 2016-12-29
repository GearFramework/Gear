<?php

namespace gear\traits;
use gear\interfaces\IObject;
use gear\interfaces\IPlugin;

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
    /**
     * @var array $_plugins список установленных компонентов
     */
    protected $_plugins = [];

    /**
     * Автозагрузка необходимых плагинов во время создания объекта-контейнера
     *
     * @param array $plugins
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _bootstrapPlugins($plugins)
    {
        foreach($plugins as $name => $plugin) {
            $this->installPlugin($name, $plugin, $this);
        }
    }

    /**
     * Возвращает инстанс плагина по его названию
     *
     * @param string $name
     * @return IPlugin
     * @since 0.0.1
     * @version 0.0.1
     */
    public function p(string $name): IPlugin
    {
        if (!($plugin = $this->isPluginInstalled($name))) {
            if (!($plugin = $this->isPluginRegistered($name))) {
                throw static::exceptionPluginNotAllowed(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
            }
            $plugin = $this->installPlugin($name, $plugin, $this);
        }
        return $plugin;
    }

    /**
     * Проверка на наличие указанного плагина. Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param string $name
     * @return bool|IPlugin
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPlugin(string $name)
    {
        if (!($plugin = isset($this->_plugins[$name]))) {
            $plugin = static::i('plugins');
            $plugin = isset($plugin[$name]) ? $this->installPlugin($name, $plugin[$name]) : false;
        }
        return $plugin;
    }

    /**
     * Возвращает конфигурационную запись плагина или false, если указанный плагин не зарегистрирован
     *
     * @param string $name
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPluginRegistered(string $name)
    {
        if (isset(static::$_config['plugins'][$name]))
            $plugin = static::$_config['plugins'][$name];
        else if (isset(self::$_config['plugins'][$name]))
            $plugin = self::$_config['plugins'][$name];
        else
            $plugin = false;
        return $plugin;
    }

    /**
     * Регистрация плагина
     *
     * @param string $name
     * @param array|\Closure $plugin
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function registerPlugin(string $name, $plugin)
    {
        if ($plugin instanceof \Closure)
            $plugin = $plugin($name);
        if (!is_array($plugin))
            throw static::exceptionPluginRegisteringIsInvalid(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        static::$_config['plugins'][$name] = $plugin;
    }

    /**
     * Возвращает инстанс установленного плагина или false, если указанный плагин не установлен
     *
     * @param string $name
     * @return bool|IPlugin
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPluginInstalled(string $name)
    {
        return isset($this->_plugins[$name]) ? $this->_plugins[$name] : false;
    }

    /**
     * Установка плагина
     *
     * @param string $name
     * @param array|IPlugin $plugin
     * @param null|IObject $owner
     * @return IPlugin
     * @since 0.0.1
     * @version 0.0.1
     */
    public function installPlugin(string $name, $plugin, $owner = null): IPlugin
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

    /**
     * Удаление установленного плагина
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallPlugin(string $name)
    {
        if (isset($this->_plugins[$name])) {
            $this->p($name)->uninstall();
            unset($this->_plugins[$name]);
        }
    }

    /**
     * Возвращает массив установленных плагинов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPlugins(): array
    {
        return $this->_plugins;
    }
}
