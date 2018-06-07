<?php

namespace Gear\Interfaces;

/**
 * Интерфейс компонента подключения к базе данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDbConnection
{
    /**
     * Завершение соединения с сервером баз данных
     *
     * @abstract
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function close(): IDbConnection;

    /**
     * Подготовка и вызов метода непосредственного подключения к серверу баз данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function connect(): IDbConnection;

    /**
     * Возвращает true если соединение уже установлено, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isConnected(): bool;

    /**
     * Подключение к серверу базы данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function open(): IDbConnection;

    /**
     * Выполняет подключение к серверу, если соединение ещё не было
     * установлено
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reconnect(): IDbConnection;

    /**
     * Выбор указанной базы данных и таблицы
     *
     * @param string $dbName
     * @param string $collectionName
     * @param string $alias
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $dbName, string $collectionName, string $alias = ''): IDbCollection;

    /**
     * Выбор указанной базы данных
     *
     * @param string $name
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(string $name): IDbDatabase;
}

/**
 * Интерфейс базы данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDbDatabase
{
    /**
     * Создание базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create(): IDbDatabase;

    /**
     * Удаление базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function drop(): IDbDatabase;

    /**
     * Удаление базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove(): IDbDatabase;

    /**
     * Выбор текущей базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function select(): IDbDatabase;

    /**
     * Возвращает коллекцию
     *
     * @param string $name
     * @param string $alias
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $name, string $alias = ''): IDbCollection;
}

interface IDbCollection
{

}

interface IDbCursor
{

}
