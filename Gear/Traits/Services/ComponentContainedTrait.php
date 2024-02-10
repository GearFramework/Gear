<?php

namespace Gear\Traits\Services;

use Gear\Core;
use Gear\Interfaces\ContainerInterface;
use Gear\Interfaces\Objects\EntityInterface;
use Gear\Interfaces\Services\ComponentInterface;
use Gear\Library\Services\Container;

/**
 * Трэйт для объектов, поддерживающих компоненты
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait ComponentContainedTrait
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected ContainerInterface|iterable|null $components = null;
    /* Public */

    /**
     * Возвращает контейнер установленных плагинов
     *
     * @return ContainerInterface|iterable
     */
    public function getComponentsContainer(): ContainerInterface|iterable
    {
        if ($this->components === null) {
            /** @var EntityInterface $this */
            $this->components = new Container($this);
        }
        return $this->components;
    }

    /**
     * Возвращает установленный плагин
     *
     * @param   string $name
     * @return  ComponentInterface|null
     */
    public function c(string $name): ?ComponentInterface
    {
        if ($component = $this->isComponentInstalled($name)) {
            return $component;
        }
        if ($componentConfig = $this->isComponentRegistered($name)) {
            $component = $this->installComponent($name, $componentConfig);
            return $component ?: null;
        }
        return null;
    }

    /**
     * Возвращает массив установленных компонентов
     *
     * @return iterable
     */
    public function getComponents(): iterable
    {
        return $this->getComponentsContainer();
    }

    /**
     * Возвращает массив зарегистрированных компонентов
     *
     * @return array
     */
    public function getRegisteredComponents(): array
    {
        return static::i('components');
    }

    /**
     * Установка плагина
     *
     * @param   string                    $name
     * @param   ComponentInterface|array  $component
     * @return  false|ComponentInterface
     */
    public function installComponent(string $name, ComponentInterface|array $component): false|ComponentInterface
    {
        if ($component instanceof ComponentInterface) {
            $this->getComponentsContainer()->set($name, $component);
            return $component;
        }
        list($class, $config, $properties) = Core::configure($component);
        $component = $class::install($config, $properties, $this);
        return $this->installComponent($name, $component);
    }

    /**
     * Проверка на наличие указанного плагина.
     * Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param   string $name
     * @return  false|ComponentInterface
     */
    public function isComponent(string $name): false|ComponentInterface
    {
        return $this->isComponentInstalled($name) || $this->isComponentRegistered($name);
    }

    /**
     * Возвращает плагин если он установлен, иначе возвращает false
     *
     * @param   string $name
     * @return  false|ComponentInterface
     */
    public function isComponentInstalled(string $name): false|ComponentInterface
    {
        return $this->getComponentsContainer()->get($name) ?: false;
    }

    /**
     * Возвращает конфигурационную запись зарегистрированного плагина, иначе возвращается false
     *
     * @param   string $name
     * @return  false|array
     */
    public function isComponentRegistered(string $name): false|array
    {
        $registeredComponents = $this->getRegisteredComponents();
        return isset($registeredComponents[$name]) ? $registeredComponents[$name] : false;
    }

    /**
     * Регистрация плагина
     *
     * @param   string $name
     * @param   array  $component
     * @return  bool
     */
    public function registerComponent(string $name, array $component): bool
    {
        $registeredComponents = $this->getRegisteredComponents();
        $registeredComponents[$name] = $component;
        static::i('components', $registeredComponents);
        return true;
    }

    /**
     * Деинсталляция плагина
     *
     * @param   string $name
     * @return  bool
     */
    public function uninstallComponent(string $name): bool
    {
        $container = $this->getComponentsContainer();
        /** @var ComponentInterface $component */
        $component = $container->get($name);
        if (empty($component)) {
            return false;
        }
        $component->uninstall();
        $container->unset($name);
        return true;
    }
}
