<?php

namespace Gear\Interfaces;

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
interface ComponentContainedInterface
{
    /**
     * Возвращает установленный компонент
     *
     * @param string $name
     * @param ObjectInterface|null $owner
     * @throws \ComponentNotFoundException
     * @return ComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function c(string $name, ObjectInterface $owner = null): ComponentInterface;

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
     * @param array|ComponentInterface|\Closure $component
     * @param ObjectInterface|null $owner
     * @return ComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function installComponent(string $name, $component, ObjectInterface $owner = null): ComponentInterface;

    /**
     * Проверка на наличие указанного компонента. Возвращает инстанс компонента или false, если такой не был найден
     *
     * @param string $name
     * @return bool|ComponentInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isComponent(string $name);

    /**
     * Возвращает компонент если он установлен, иначе возвращает false
     *
     * @param string $name
     * @return bool|ComponentInterface
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
 * Интерфейс объектов, которые зависят от других объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DependentInterface {}

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
interface ModelInterface extends ObjectInterface
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
 * Интерфейс базовых объектов приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ObjectInterface
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
     * @return ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOwner(): ?ObjectInterface;

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
     * @param ObjectInterface $owner
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setOwner(ObjectInterface $owner);
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
interface PluginContainedInterface
{
    /**
     * Возвращает установленный плагин
     *
     * @param string $name
     * @param ObjectInterface|null $owner
     * @throws \PluginNotFoundException
     * @return PluginInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function p(string $name, ObjectInterface $owner = null): PluginInterface;

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
     * @param array|PluginInterface|\Closure $plugin
     * @param ObjectInterface|null $owner
     * @return PluginInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function installPlugin(string $name, $plugin, ObjectInterface $owner = null): PluginInterface;

    /**
     * Проверка на наличие указанного плагина.
     * Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param string $name
     * @return bool|PluginInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPlugin(string $name);

    /**
     * Возвращает плагин если он установлен, иначе возвращает false
     *
     * @param string $name
     * @return bool|PluginInterface
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
