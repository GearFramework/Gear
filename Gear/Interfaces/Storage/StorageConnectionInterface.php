<?php

namespace Gear\Interfaces\Storage;

use Gear\Interfaces\Services\ComponentInterface;

/**
 * Интерфейс компонентов для соединения с хранилищами
 * данных (БД, кэш и пр.)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface StorageConnectionInterface extends StorageInterface
{
    /**
     * Возвращает текущее хранилище данных
     *
     * @return StorageConnectionInterface
     */
    public function getConnection(): StorageConnectionInterface;

    /**
     * Возвращает текущее пространство с коллекциям данных
     *
     * @param   string $spaceName
     * @return  StorageSpaceInterface|null
     */
    public function getSpace(string $spaceName): ?StorageSpaceInterface;

    /**
     * Возвращает текущую коллекцию, которая непосредственно хранит данные
     * (например таблица в SQL)
     *
     * @param   string $spaceName
     * @param   string $collectionName
     * @return  StorageCollectionInterface|null
     */
    public function getCollection(string $spaceName, string $collectionName): ?StorageCollectionInterface;

    /**
     * Возвращает true, если соединение установлено, иначе
     * возвращает false
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * Выполняет подключение к хранилищу с данными
     * В случае успешного оединения возвращает true, иначе - false
     *
     * @return bool
     */
    public function connect(): bool;

    /**
     * Пингует соединение с хранилищем данных и возвращает
     * true, если подключение существует
     * false, если соединение было разорвано
     *
     * @return bool
     */
    public function ping(): bool;

    /**
     * Выполняет отключение от хранилища с данными
     *
     * @return bool
     */
    public function disconnect(): bool;
}
