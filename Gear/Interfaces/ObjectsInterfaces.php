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
 * @property mixed primaryKey
 * @property string|array primaryKeyName
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ModelInterface {}

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
     * Получение названия шаблонизатора, записанного в конфигурации класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewerName(): string;

    /**
     * Возвращает путь, по которому лежат шаблоны отображения объекта
     *
     * @return string|DirectoryInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewPath();

    /**
     * Возвращает путь, по которому лежит основной макет
     *
     * @return string|DirectoryInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewLayout();

    /**
     * Отображение шаблона
     *
     * @param $template
     * @param array $context
     * @param bool $buffered
     * @return bool|string
     * @since 0.0.1
     * @version 0.0.1
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
     * @version 0.0.1
     */
    public function renderFile(string $filePath, array $context = [], bool $buffered = false): bool|string;
}
