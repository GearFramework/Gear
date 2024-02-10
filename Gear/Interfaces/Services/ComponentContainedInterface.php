<?php


namespace Gear\Interfaces\Services;

use Gear\Interfaces\Objects\ModelInterface;

/**
 * Интерфейс объектов, поддерживающих компоненты
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ComponentContainedInterface extends ModelInterface
{
    /**
     * Возвращает установленный плагин
     *
     * @param   string $name
     * @return  ComponentInterface|null
     */
    public function c(string $name): ?ComponentInterface;

    /**
     * Возвращает массив установленных компонентов
     *
     * @return iterable
     */
    public function getComponents(): iterable;

    /**
     * Возвращает массив зарегистрированных компонентов
     *
     * @return iterable
     */
    public function getRegisteredComponents(): iterable;

    /**
     * Установка компонента
     *
     * @param   string $name
     * @param   array|ComponentInterface $component
     * @return  false|ComponentInterface
     */
    public function installComponent(string $name, array|ComponentInterface $component): false|ComponentInterface;

    /**
     * Проверка на наличие указанного компонента. Возвращает инстанс компонента или false, если такой не был найден
     *
     * @param   string $name
     * @return  false|ComponentInterface
     */
    public function isComponent(string $name): false|ComponentInterface;

    /**
     * Возвращает компонент если он установлен, иначе возвращает false
     *
     * @param   string $name
     * @return  false|ComponentInterface
     */
    public function isComponentInstalled(string $name): false|ComponentInterface;

    /**
     * Возвращает конфигурационную запись зарегистрированного компонента, иначе возвращается false
     *
     * @param   string $name
     * @return  false|array
     */
    public function isComponentRegistered(string $name): false|array;

    /**
     * Регистрация компонента
     *
     * @param   string $name
     * @param   array $component
     * @return  bool
     */
    public function registerComponent(string $name, array $component): bool;

    /**
     * Деинсталляция компонента
     *
     * @param   string $name
     * @return  bool
     */
    public function uninstallComponent(string $name): bool;
}
