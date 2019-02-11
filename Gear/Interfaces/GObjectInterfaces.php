<?php

namespace Gear\Interfaces;

/**
 * Интерфейс базовых объектов приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GObjectInterface
{
    /**
     * Возвращает пространство имен класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getNamespace(): string;

    /**
     * Возвращает владельца объекта
     *
     * @return GObjectInterface|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOwner(): ?GObjectInterface;

    /**
     * Получение или установка значения для указанного свойства объекта. При отсутствии параметров возвращает
     * массив всех свойст объекта
     *
     * @param null|string $name
     * @param mixed $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function props($name = null, $value = null);

    /**
     * Установка владельца объекта
     *
     * @param GObjectInterface $owner
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setOwner(GObjectInterface $owner);
}

/**
 * Интерфейс моделей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GModelInterface extends GObjectInterface
{
    /**
     * Возвращает значение поля, которое является первичным ключом
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrimaryKey();

    /**
     * Возвращает название поля, которое является первичным ключом
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrimaryKeyName(): string;

    /**
     * Устанавливает значение для поля, которое является первичным ключом
     *
     * @param mixed $value
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setPrimaryKey($value);

    /**
     * Устанавливает название поля, которое является первичным ключом
     *
     * @param string $pkName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setPrimaryKeyName(string $pkName);
}

/**
 * Интерфейс модели с фиксированной схемой свойств
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface SchemaInterface
{
    /**
     * Взвращает схему модели
     *
     * @access public
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSchema(): array;

    /**
     * Взвращает названия свойств модели
     *
     * @access public
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSchemaNames(): array;

    /**
     * Взвращает значения свойств модели
     *
     * @access public
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSchemaValues(): array;
}

/**
 * Интерфейс объектов, которые зависят от других объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GDependentInterface {}

/**
 * Интерфейс объектов, поддерживающих компоненты
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GComponentContainedInterface
{
    /**
     * Возвращает установленный компонент
     *
     * @param string $name
     * @param GObjectInterface|null $owner
     * @throws \ComponentNotFoundException
     * @return GComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function c(string $name, GObjectInterface $owner = null): GComponentInterface;

    /**
     * Возвращает массив установленных компонентов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getComponents(): array;

    /**
     * Возвращает массив зарегистрированных компонентов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRegisteredComponents(): array;

    /**
     * Установка компонента
     *
     * @param string $name
     * @param array|GComponentInterface|\Closure $component
     * @param GObjectInterface|null $owner
     * @return GComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function installComponent(string $name, $component, GObjectInterface $owner = null): GComponentInterface;

    /**
     * Проверка на наличие указанного компонента. Возвращает инстанс компонента или false, если такой не был найден
     *
     * @param string $name
     * @return bool|GComponentInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isComponent(string $name);

    /**
     * Возвращает компонент если он установлен, иначе возвращает false
     *
     * @param string $name
     * @return bool|GComponentInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isComponentInstalled(string $name);

    /**
     * Возвращает конфигурационную запись зарегистрированного компонента, иначе возвращается false
     *
     * @param string $name
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isComponentRegistered(string $name);

    /**
     * Регистрация компонента
     *
     * @param string $name
     * @param array|\Closure $component
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function registerComponent(string $name, $component);

    /**
     * Деинсталляция компонента
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallComponent(string $name);
}

/**
 * Интерфейс объектов, поддерживающих плагины
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface IPluginContained
{
    /**
     * Возвращает установленный плагин
     *
     * @param string $name
     * @param GObjectInterface|null $owner
     * @throws \PluginNotFoundException
     * @return GPluginInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function p(string $name, GObjectInterface $owner = null): GPluginInterface;

    /**
     * Возвращает массив установленных плагинов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPlugins(): array;

    /**
     * Возвращает массив зарегистрированных плагинов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRegisteredPlugins(): array;

    /**
     * Установка плагина
     *
     * @param string $name
     * @param array|GPluginInterface|\Closure $plugin
     * @param GObjectInterface|null $owner
     * @return GPluginInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function installPlugin(string $name, $plugin, GObjectInterface $owner = null): GPluginInterface;

    /**
     * Проверка на наличие указанного плагина.
     * Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param string $name
     * @return bool|GPluginInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPlugin(string $name);

    /**
     * Возвращает плагин если он установлен, иначе возвращает false
     *
     * @param string $name
     * @return bool|GPluginInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPluginInstalled(string $name);

    /**
     * Возвращает конфигурационную запись зарегистрированного плагина, иначе возвращается false
     *
     * @param string $name
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPluginRegistered(string $name);

    /**
     * Регистрация плагина
     *
     * @param string $name
     * @param array|\Closure $plugin
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function registerPlugin(string $name, $plugin);

    /**
     * Деинсталляция плагина
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallPlugin(string $name);
}
