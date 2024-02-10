<?php

namespace Gear\Library\Storage;

use Gear\Interfaces\Storage\StorageConnectionInterface;
use Gear\Interfaces\Storage\StorageSpaceInterface;
use Gear\Library\Objects\Model;
use IteratorAggregate;

/**
 * Модель пространства (например база данных в SQL), где хранятся данные в коллекциях
 *
 * @property StorageConnectionInterface|null $owner
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class StorageSpace extends Model implements IteratorAggregate, StorageSpaceInterface
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
        return $this->owner;
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
     * Возвращает текущее пространство
     *
     * @return StorageSpaceInterface
     */
    public function getSpace(): StorageSpaceInterface
    {
        return $this;
    }
}
