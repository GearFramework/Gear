<?php

namespace Gear\Interfaces;

/**
 * Интерфейс компонента автозагрузки классов
 *
 * @package Gear Framework
 *
 * @property array aliases
 * @property array paths
 * @property array resolvePaths
 * @property bool usePaths
 * @property bool useResolvePaths
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface AutoloaderInterface
{
    /**
     * Возвращает оригинальное название класса, которому соответствует указанный алиас
     *
     * @param string $alias
     * @return null|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAlias(string $alias);

    /**
     * Возвращает список алиасов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAliases(): array;

    /**
     * Возвращает массив с указанием директорий с соответсвующих им классом
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPaths(): array;

    /**
     * Возвращает массив соответствия пути и значением того, как должен "резолвится"
     * (преобразовываться) этот путь
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getResolvePaths(): array;

    /**
     * Возвращает true, если будет использоваться массив путей к классам
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUsePaths(): bool;

    /**
     * Возвращает true, если будет использоваться массив преобразования путей и пространств имен
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUseResolvePaths(): bool;

    /**
     * Метод автоматической загрузки классов
     * - Поддержка алиасов
     * - Поддержка пользовательских путей расположения файлов с классами
     *
     * @param string $className
     * @since 0.0.1
     * @version 0.0.1
     */
    public function loader(string $className);

    /**
     * Обработчик события onInstalled по-умолчанию
     * Регистрация метода автозагрузки классов
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function onAfterInstallService($event);

    /**
     * Получение физического пути к уазанному элементу (файл, директория).
     *
     * @param string $namespace
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function resolvePath(string $namespace): string;
}