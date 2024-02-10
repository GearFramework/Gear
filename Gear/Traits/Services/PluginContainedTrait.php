<?php

namespace Gear\Traits\Services;

use Gear\Core;
use Gear\Interfaces\ContainerInterface;
use Gear\Interfaces\Objects\EntityInterface;
use Gear\Interfaces\Services\PluginInterface;
use Gear\Library\Services\Container;

/**
 * Трэйт для объектов, поддерживающих плагины
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait PluginContainedTrait
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected ContainerInterface|iterable|null $plugins = null;
    /* Public */

    /**
     * Возвращает контейнер установленных плагинов
     *
     * @return ContainerInterface|iterable
     */
    public function getPluginsContainer(): ContainerInterface|iterable
    {
        if ($this->plugins === null) {
            /** @var EntityInterface $this */
            $this->plugins = new Container($this);
        }
        return $this->plugins;
    }

    /**
     * Возвращает установленный плагин
     *
     * @param   string $name
     * @return  PluginInterface|null
     */
    public function p(string $name): ?PluginInterface
    {
        if ($plugin = $this->isPluginInstalled($name)) {
            return $plugin;
        }
        if ($pluginConfig = $this->isPluginRegistered($name)) {
            $plugin = $this->installPlugin($name, $pluginConfig);
            return $plugin ?: null;
        }
        return null;
    }

    /**
     * Возвращает массив установленных плагинов
     *
     * @return iterable
     */
    public function getPlugins(): iterable
    {
        return $this->getPluginsContainer();
    }

    /**
     * Возвращает массив зарегистрированных плагинов
     *
     * @return array
     */
    public function getRegisteredPlugins(): array
    {
        return static::i('plugins');
    }

    /**
     * Установка плагина
     *
     * @param   string                $name
     * @param   PluginInterface|array $plugin
     * @return  false|PluginInterface
     */
    public function installPlugin(string $name, PluginInterface|array $plugin): false|PluginInterface
    {
        if ($plugin instanceof PluginInterface) {
            $this->getPluginsContainer()->set($name, $plugin);
            return $plugin;
        }
        list($class, $config, $properties) = Core::configure($plugin);
        $plugin = $class::install($config, $properties, $this);
        if ($plugin) {
            $this->getPluginsContainer()->set($name, $plugin);
            return $plugin;
        }
        return false;
    }

    /**
     * Проверка на наличие указанного плагина.
     * Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param   string $name
     * @return  false|PluginInterface
     */
    public function isPlugin(string $name): false|PluginInterface
    {
        return $this->isPluginInstalled($name) || $this->isPluginRegistered($name);
    }

    /**
     * Возвращает плагин если он установлен, иначе возвращает false
     *
     * @param   string $name
     * @return  false|PluginInterface
     */
    public function isPluginInstalled(string $name): false|PluginInterface
    {
        return $this->getPluginsContainer()->get($name) ?: false;
    }

    /**
     * Возвращает конфигурационную запись зарегистрированного плагина, иначе возвращается false
     *
     * @param   string $name
     * @return  false|array
     */
    public function isPluginRegistered(string $name): false|array
    {
        $registeredPlugins = $this->getRegisteredPlugins();
        return isset($registeredPlugins[$name]) ? $registeredPlugins[$name] : false;
    }

    /**
     * Регистрация плагина
     *
     * @param   string $name
     * @param   array  $plugin
     * @return  bool
     */
    public function registerPlugin(string $name, array $plugin): bool
    {
        $registeredPlugins = $this->getRegisteredPlugins();
        $registeredPlugins[$name] = $plugin;
        static::i('plugins', $registeredPlugins);
        return true;
    }

    /**
     * Деинсталляция плагина
     *
     * @param   string $name
     * @return  bool
     */
    public function uninstallPlugin(string $name): bool
    {
        $container = $this->getPluginsContainer();
        /** @var PluginInterface $plugin */
        $plugin = $container->get($name);
        if (empty($plugin)) {
            return false;
        }
        $plugin->uninstall();
        $container->unset($name);
        return true;
    }
}
