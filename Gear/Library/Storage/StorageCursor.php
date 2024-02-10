<?php

namespace Gear\Library\Storage;

use Gear\Interfaces\Storage\StorageCollectionInterface;
use Gear\Interfaces\Storage\StorageConnectionInterface;
use Gear\Interfaces\Storage\StorageCursorInterface;
use Gear\Interfaces\Storage\StorageSpaceInterface;
use Gear\Library\Objects\Model;
use Iterator;

/**
 * Модель, которая итерирует по данным, извлекает из коллекции
 * по указанным условиям, сортирует, группирует и пр.
 *
 * @property StorageConnectionInterface|StorageSpaceInterface|StorageCollectionInterface $owner
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class StorageCursor extends Model implements Iterator, StorageCursorInterface
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
        return $this->owner->getConnection();
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
     * @return StorageSpaceInterface|null
     */
    public function getSpace(): ?StorageSpaceInterface
    {
        if ($this->owner instanceof StorageCollectionInterface) {
            return $this->owner->getSpace();
        }
        if ($this->owner instanceof StorageSpaceInterface) {
            return $this->owner;
        }
        return null;
    }

    /**
     * Возвращает текущую коллекцию в которой функционирует курсор
     *
     * @return StorageCollectionInterface|null
     */
    public function getCollection(): ?StorageCollectionInterface
    {
        if ($this->owner instanceof StorageCollectionInterface) {
            return $this->owner;
        }
        return null;
    }
}
