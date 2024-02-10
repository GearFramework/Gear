<?php

namespace Gear\Library\Storage;

use Gear\Interfaces\Storage\StorageCollectionInterface;
use Gear\Interfaces\Storage\StorageConnectionInterface;
use Gear\Interfaces\Storage\StorageSpaceInterface;
use Gear\Library\Objects\Model;
use IteratorAggregate;

/**
 * Модель, которая непосредственно хранит данные
 * (например в таблице SQL)
 *
 * @property StorageSpaceInterface $owner
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class StorageCollection extends Model implements IteratorAggregate, StorageCollectionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает хранилище данных (подключение), в котором находится пространство коллекций
     *
     * @return StorageConnectionInterface
     */
    public function getConnection(): StorageConnectionInterface
    {
        return $this->getSpace()->getConnection();
    }

    /**
     * Возвращает подключение к хранилищу
     *
     * @return mixed
     */
    public function getHandler(): mixed
    {
        return $this->getConnection()->getHandler();
    }

    /**
     * Возвращает пространство, в котором находится коллекция
     *
     * @return StorageSpaceInterface
     */
    public function getSpace(): StorageSpaceInterface
    {
        return $this->owner;
    }

    /**
     * Возвращает текущую коллекцию
     *
     * @return StorageCollectionInterface
     */
    public function getCollection(): StorageCollectionInterface
    {
        return $this;
    }
}
