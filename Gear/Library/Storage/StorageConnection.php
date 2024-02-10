<?php

namespace Gear\Library\Storage;

use Gear\Interfaces\Storage\StorageConnectionInterface;
use Gear\Library\Objects\Model;
use Gear\Library\Services\Component;
use IteratorAggregate;

/**
 * Абстрактный компонент для соединения с хранилищами
 * данных (БД, кэш и пр.)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class StorageConnection extends Component implements IteratorAggregate, StorageConnectionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected mixed $handler = null;
    /* Public */

    /**
     * Возвращает текущее хранилище данных
     *
     * @return StorageConnectionInterface
     */
    public function getConnection(): StorageConnectionInterface
    {
        return $this;
    }

    /**
     * Возвращает подключение к хранилищу
     *
     * @return mixed
     */
    public function getHandler(): mixed
    {
        return $this->handler;
    }

    /**
     * Установка ресурса подключенного хранилища
     *
     * @param   mixed $handler
     * @return  void
     */
    protected function setHandler(mixed $handler): void
    {
        $this->handler = $handler;
    }
}
