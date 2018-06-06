<?php

namespace Gear\Traits;

use Arquivo\Models\RgSource;
use Gear\Core;
use Gear\Interfaces\IObject;
use Gear\Interfaces\IPlugin;

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
        foreach ($plugins as $name => $plugin) {
            $this->installPlugin($name, $plugin, $this);
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

    /**
     * Возвращает массив зарегистрированных плагинов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRegisteredPlugins(): array
    {
        return static::i('plugins');
    }

    /**
     * Установка плагина
     *
     * @param string $name
     * @param array|IComponent|\Closure $plugin
     * @param null|IObject $owner
     * @return IPlugin
     * @since 0.0.1
     * @version 0.0.1
     */
    public function installPlugin(string $name, $plugin, IObject $owner = null): IPlugin
    {
        $owner = $owner ?: $this;
        if ($plugin instanceof \Closure) {
            $plugin = $plugin($name, $owner);
        }
        if (is_array($plugin)) {
            list($class, $config, $properties) = Core::configure($plugin);
            $plugin = $class::install($config, $properties, $owner ?: $this);
        }
        if (!($plugin instanceof IPlugin)) {
            throw static::PluginInstallationIsInvalidException(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        }
        $this->_plugins[$name] = $plugin;
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
        elseif (isset(self::$_config['plugins'][$name]))
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
            throw static::PluginRegisteringIsInvalidException(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        static::$_config['plugins'][$name] = $plugin;
    }

    /**
     * Возвращает инстанс плагина по его названию
     *
     * @param string $name
     * @param IObject|null $owner
     * @throws \PluginNotFoundException
     * @return IPlugin
     * @since 0.0.1
     * @version 0.0.1
     */
    public function p(string $name, IObject $owner = null): IPlugin
    {
        if ($owner === null) {
            $owner = $this;
        }
        if (!($plugin = $this->isPluginInstalled($name))) {
            if (!($plugin = $this->isPluginRegistered($name))) {
                throw static::PluginNotFoundException(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
            }
            $plugin = $this->installPlugin($name, $plugin, $owner);
        }
        return $plugin;
    }

    /**
     * Удаление установленного плагина
     *
     * @param string $name
     * @return void
     * @throws \PluginNotFoundException
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
}
