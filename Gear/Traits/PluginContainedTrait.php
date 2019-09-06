<?php

namespace Gear\Traits;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;
use Gear\Interfaces\PluginInterface;
use VideoRg\Components\Paginator\RgPaginatorComponent;

/**
 * Трэйт для объектов, которым необходимо поддерживать плагины
 *
 * @package Gear Framework
 *
 * @property array plugins
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait PluginContainedTrait
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
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRegisteredPlugins(): iterable
    {
        return static::i('plugins');
    }

    /**
     * Установка плагина
     *
     * @param string $name
     * @param array|PluginInterface|\Closure $plugin
     * @param null|ObjectInterface $owner
     * @return PluginInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function installPlugin(string $name, $plugin, ObjectInterface $owner = null): PluginInterface
    {
        $owner = $owner ?: $this;
        if ($plugin instanceof \Closure) {
            $plugin = $plugin($name, $owner);
        }
        if (is_array($plugin)) {
            list($class, $config, $properties) = Core::configure($plugin);
            $plugin = $class::install($config, $properties, $owner ?: $this);
        }
        if (!($plugin instanceof PluginInterface)) {
            throw static::PluginInstallationIsInvalidException(['name' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
        }
        $this->_plugins[$name] = $plugin;
        /** @var PluginInterface $plugin */
        return $plugin;
    }

    /**
     * Проверка на наличие указанного плагина. Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param string $name
     * @return bool|PluginInterface
     * @since 0.0.1
     * @version 0.0.2
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
     * @return bool|PluginInterface
     * @since 0.0.1
     * @version 0.0.2
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
     * @param ObjectInterface|null $owner
     * @throws \PluginNotFoundException
     * @return PluginInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function p(string $name, ObjectInterface $owner = null): PluginInterface
    {
        if ($owner === null) {
            $owner = $this;
        }
        if (!($plugin = $this->isPluginInstalled($name))) {
            if (!($plugin = $this->isPluginRegistered($name))) {
                throw static::PluginNotFoundException(['pluginName' => $name, 'class' => get_class($this), 'file' => __FILE__, 'line' => __LINE__]);
            }
            $plugin = $this->installPlugin($name, $plugin, $owner);
        }
        /**
         * @var PluginInterface $plugin
         */
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
