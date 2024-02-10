<?php

namespace Gear\Interfaces\Storage;

/**
 * Интерфейс моделей пространства (например база данных в SQL), где хранятся данные в коллекциях
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface StorageSpaceInterface extends StorageInterface
{
    /**
     * Возвращает хранилище данных (подключение), в котором находится пространство коллекций
     *
     * @return StorageConnectionInterface
     */
    public function getConnection(): StorageConnectionInterface;

    /**
     * Возвращает текущее пространство
     *
     * @return StorageSpaceInterface
     */
    public function getSpace(): StorageSpaceInterface;

    /**
     * Возвращает текущую коллекцию, которая непосредственно хранит данные
     * (например таблица в SQL)
     *
     * @param   string $collectionName
     * @return  StorageCollectionInterface|null
     */
    public function getCollection(string $collectionName): ?StorageCollectionInterface;
}
