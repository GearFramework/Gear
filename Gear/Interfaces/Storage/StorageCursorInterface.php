<?php

namespace Gear\Interfaces\Storage;

/**
 * Интерфейс элемента, который итерирует по данным, извлекает из коллекции
 * по указанным условиям, сортирует, группирует и пр.
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface StorageCursorInterface extends StorageInterface
{
    /**
     * Возвращает хранилище данных (подключение), в котором находится пространство коллекции
     *
     * @return StorageConnectionInterface
     */
    public function getConnection(): StorageConnectionInterface;

    /**
     * Возвращает подключение к хранилищу
     *
     * @return mixed
     */
    public function getHandler(): mixed;

    /**
     * Возвращает пространство, в котором находится коллекция
     *
     * @return StorageSpaceInterface|null
     */
    public function getSpace(): ?StorageSpaceInterface;

    /**
     * Возвращает текущую коллекцию в которой функционирует курсор
     *
     * @return StorageCollectionInterface|null
     */
    public function getCollection(): ?StorageCollectionInterface;
}
