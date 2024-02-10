<?php

namespace Gear\Interfaces\Services;

use Gear\Interfaces\Objects\ModelInterface;

/**
 * Интерфейс объектов, поддерживающих плагины
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface PluginContainedInterface extends ModelInterface
{
    /**
     * Возвращает установленный плагин
     *
     * @param   string $name
     * @return  PluginInterface|null
     */
    public function p(string $name): ?PluginInterface;

    /**
     * Возвращает массив установленных плагинов
     *
     * @return iterable
     */
    public function getPlugins(): iterable;

    /**
     * Возвращает массив зарегистрированных плагинов
     *
     * @return array
     */
    public function getRegisteredPlugins(): array;

    /**
     * Установка плагина
     *
     * @param   string $name
     * @param   PluginInterface|array $plugin
     * @return  false|PluginInterface
     */
    public function installPlugin(string $name, PluginInterface|array $plugin): false|PluginInterface;

    /**
     * Проверка на наличие указанного плагина.
     * Возвращает инстанс плагина или false, если такой не был найден
     *
     * @param   string $name
     * @return  false|PluginInterface
     */
    public function isPlugin(string $name): false|PluginInterface;

    /**
     * Возвращает плагин если он установлен, иначе возвращает false
     *
     * @param   string $name
     * @return  false|PluginInterface
     */
    public function isPluginInstalled(string $name): false|PluginInterface;

    /**
     * Возвращает конфигурационную запись зарегистрированного плагина, иначе возвращается false
     *
     * @param   string $name
     * @return  false|array
     */
    public function isPluginRegistered(string $name): false|array;

    /**
     * Регистрация плагина
     *
     * @param   string $name
     * @param   array $plugin
     * @return  bool
     */
    public function registerPlugin(string $name, array $plugin): bool;

    /**
     * Деинсталляция плагина
     *
     * @param   string $name
     * @return  bool
     */
    public function uninstallPlugin(string $name): bool;
}
