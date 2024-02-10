<?php

namespace Gear\Interfaces\Storage;

/**
 * Интерфейс моделей, которые непосредственно хранят данные
 * (например в таблице SQL)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface StorageCollectionInterface extends StorageInterface
{
    /**
     * Возвращает хранилище данных (подключение), в котором находится коллекция
     *
     * @return StorageConnectionInterface
     */
    public function getConnection(): StorageConnectionInterface;

    /**
     * Возвращает пространство, в котором находится коллекция
     *
     * @return StorageSpaceInterface
     */
    public function getSpace(): StorageSpaceInterface;

    /**
     * Возвращает текущую коллекцию
     *
     * @return StorageCollectionInterface
     */
    public function getCollection(): StorageCollectionInterface;
}
