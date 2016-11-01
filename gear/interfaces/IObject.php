<?php

namespace gear\interfaces;

/**
 * Интерфейс базовых объектов приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IObject
{
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
}

/**
 * Интерфейс моделей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IModel extends IObject {}

/**
 * Интерфейс модели с фиксированной схемой свойств
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface ISchema
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
 * Общий интерфейс поведений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IBehavior
{
    /**
     * Установка поведения
     *
     * @param array $properties
     * @param IObject|object $owner
     * @return IBehavior
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function install($properties = [], IObject $owner): IBehavior;

    /**
     * Запуск поведения
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke();

    /**
     * Запуск поведения
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function execute();

    /**
     * Удаление поведения из объекта-владельца
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstall();
}

/**
 * Интерфейс объектов, которые зависят от других объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDependent {}
