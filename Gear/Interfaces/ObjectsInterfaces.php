<?php

namespace Gear\Interfaces;

/**
 * Интерфейс базовых объектов приложения
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ObjectInterface {}

/**
 * Интерфейс моделей
 *
 * @package Gear Framework 2
 *
 * @property string|array primaryKey
 * @property int|float|string|array primaryKeyValue
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ModelInterface
{
    /**
     * Возвращает значение первичного ключа модели, возвращает массив в
     * случае составного ключа
     *
     * @return mixed
     * @since 2.0.0
     * @version 2.0.0
     */
    public function getPrimaryKeyValue(): mixed;

    /**
     * Возвращает название поля, которое выступает первичным ключом,
     * возвращает массив в случае составного ключа
     *
     * @return string|array
     * @since 2.0.0
     * @version 2.0.0
     */
    public function getPrimaryKey(): string|array;

    /**
     * Устанавливает название поля, которое выступает первичным ключом,
     * передача в аргументе массива в случае составного ключа
     *
     * @param string|array $primaryKeyName
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public function setPrimaryKey(string|array $primaryKeyName): void;
}

/**
 * Интерфейс модели с фиксированной схемой свойств
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface SchemaInterface
{
    const TYPE_BOOL = 'boolean';
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';

    /**
     * Возвращает схему модели
     *
     * @return array
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getSchema(): array;

    /**
     * Возвращает названия свойств модели
     *
     * @return array
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getSchemaNames(): array;

    /**
     * Возвращает значения свойств модели
     *
     * @return array
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getSchemaValues(): array;
}

/**
 * Интерфейс объектов, умеющих себе отображать
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 2.0.0
 */
interface ViewableInterface
{
    /**
     * Возвращает сервис, который занимается отображением шаблонов
     * Генерирует исключение ViewerNotFoundException, если сервис не зарегистрирован в
     * конфиге объекте
     *
     * @return ViewerInterface
     * @since 2.0.0
     * @version 2.0.0
     */
    public function getViewer(): ViewerInterface;

    /**
     * Получение названия шаблонизатора, записанного в конфигурации класса
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getViewerName(): string;

    /**
     * Возвращает путь, по которому лежат шаблоны отображения объекта
     *
     * @return string|DirectoryInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getViewPath(): string|DirectoryInterface;

    /**
     * Возвращает путь, по которому лежит основной макет
     *
     * @return string|DirectoryInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getViewLayout(): string|DirectoryInterface;

    /**
     * Отображение шаблона
     *
     * @param mixed $template
     * @param array $context
     * @param bool $buffered
     * @return bool|string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function render($template, array $context = [], bool $buffered = false): bool|string;

    /**
     * Подключение .phtml файла с шаблоном отображения
     *
     * @param string|FileInterface $filePath
     * @param array $context
     * @param bool $buffered
     * @return bool|string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function renderFile(string $filePath, array $context = [], bool $buffered = false): bool|string;
}

/**
 * Интерфейс объектов, поддерживающих компоненты
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ContainComponentsInterface
{
    /**
     * Возвращает установленный компонент
     * Генерирует исключение ComponentNotFoundException если компонент
     * не установлен
     *
     * @param string $name
     * @param ObjectInterface|null $owner
     * @return ComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function c(string $name, ?ObjectInterface $owner = null): ComponentInterface;

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
     * Возвращает регистрационные данные указанного компонента
     *
     * @param string $name
     * @return array|null
     * @since 2.0.0
     * @version 2.0.0
     */
    public function getComponentRegistration(string $name): ?array;

    /**
     * Установка компонента
     *
     * @param string $name
     * @param array|ComponentInterface $component
     * @param ObjectInterface|null $owner
     * @return ComponentInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function installComponent(
        string $name,
        array|ComponentInterface $component,
        ?ObjectInterface $owner = null
    ): ComponentInterface;

    /**
     * Проверка на наличие указанного компонента. Возвращает инстанс компонента или false, если такой не был найден
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function isComponent(string $name): bool;

    /**
     * Возвращает компонент если он установлен, иначе возвращает false
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function isComponentInstalled(string $name): bool;

    /**
     * Возвращает конфигурационную запись зарегистрированного компонента, иначе возвращается false
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function isComponentRegistered(string $name): bool;

    /**
     * Регистрация компонента
     *
     * @param string $name
     * @param array $component
     * @return void
     * @since 0.0.1
     * @version 2.0.0
     */
    public function registerComponent(string $name, array $component): void;

    /**
     * Деинсталляция компонента
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 2.0.0
     */
    public function uninstallComponent(string $name): void;
}

/**
 * Интерфейс объектов, поддерживающих плагины
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ContainPluginsInterface
{
    /**
     * Возвращает установленный плагин
     * Генерирует исключение PluginNotFoundException если плагин
     * не установлен
     *
     * @param string $name
     * @param ObjectInterface|null $owner
     * @return PluginInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function p(string $name, ?ObjectInterface $owner = null): PluginInterface;

    /**
     * Возвращает массив установленных плагинов
     *
     * @return array
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getPlugins(): array;

    /**
     * Возвращает массив зарегистрированных плагинов
     *
     * @return iterable
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getRegisteredPlugins(): iterable;

    /**
     * Возвращает регистрационные данные указанного плагина
     *
     * @param string $name
     * @return array|null
     * @since 2.0.0
     * @version 2.0.0
     */
    public function getPluginRegistration(string $name): ?array;

    /**
     * Установка плагина
     *
     * @param string $name
     * @param array|PluginInterface $plugin
     * @param ObjectInterface|null $owner
     * @return PluginInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function installPlugin(
        string $name,
        array|PluginInterface $plugin,
        ?ObjectInterface $owner = null,
    ): PluginInterface;

    /**
     * Проверка на наличие указанного плагина.
     * Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function isPlugin(string $name): bool;

    /**
     * Возвращает плагин если он установлен, иначе возвращает false
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function isPluginInstalled(string $name): bool;

    /**
     * Возвращает конфигурационную запись зарегистрированного плагина, иначе возвращается false
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function isPluginRegistered(string $name): bool;

    /**
     * Регистрация плагина
     *
     * @param string $name
     * @param array $plugin
     * @return void
     * @since 0.0.1
     * @version 2.0.0
     */
    public function registerPlugin(string $name, array $plugin): void;

    /**
     * Деинсталляция плагина
     *
     * @param string $name
     * @return void
     * @since 0.0.1
     * @version 2.0.0
     */
    public function uninstallPlugin(string $name): void;
}
