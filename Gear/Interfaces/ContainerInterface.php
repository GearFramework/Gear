<?php

namespace Gear\Interfaces;

/**
 * Интерфейс контейнеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ContainerInterface
{
    /**
     * Получение элемента из контейнера по ключу
     *
     * @param   string $key
     * @return  mixed
     */
    public function get(string $key): mixed;

    /**
     * Установка в коллекцию элемента с соответствующим ключем
     *
     * @param   string  $key
     * @param   mixed   $entity
     * @return  void
     */
    public function set(string $key, mixed $entity): void;

    /**
     * Проверка наличия элемента в коллекции с соответствующим ключём
     *
     * @param   string $key
     * @return  bool
     */
    public function isset(string $key): bool;

    /**
     * Удаление из коллекции элемента с соответствующим ключём
     *
     * @param   string $key
     * @return  void
     */
    public function unset(string $key): void;
}
