<?php

namespace Gear\Library\Services;

use ArrayAccess;
use Countable;
use Gear\Interfaces\ContainerInterface;
use Gear\Interfaces\Objects\EntityInterface;
use Gear\Interfaces\Objects\ModelInterface;
use Gear\Interfaces\Templater\ViewableInterface;
use Gear\Traits\Objects\ModelTrait;
use Gear\Traits\Objects\ViewableTrait;
use Gear\Traits\Types\ArrayAccessTrait;
use Gear\Traits\Types\CountableTrait;
use Gear\Traits\Types\IteratorTrait;
use Iterator;

/**
 * Контейнер
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Container implements ContainerInterface, Iterator, ArrayAccess, Countable, ViewableInterface
{
    /* Traits */
    use ModelTrait;
    use IteratorTrait;
    use ArrayAccessTrait;
    use CountableTrait;
    use ViewableTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected array $items;
    /* Public */

    /**
     * Конструктор объекта. На вход принимает массив свойств и объект, который является владельцем
     * данного объекта
     *
     * @param ModelInterface|null $owner
     */
    public function __construct(ModelInterface $owner = null)
    {
        $this->items = [];
        $this->setOwner($owner);
    }

    /**
     * Получение элемента из контейнера по ключу
     *
     * @param   string $key
     * @return  mixed
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * Получение элемента из контейнера по ключу
     *
     * @param   string $key
     * @return  mixed
     */
    public function get(string $key): mixed
    {
        return $this->offsetGet($key);
    }

    /**
     * Установка в коллекцию элемента с соответствующим ключем
     *
     * @param   string $key
     * @param   mixed $entity
     * @return  void
     */
    public function __set(string $key, mixed $entity): void
    {
        $this->set($key, $entity);
    }

    /**
     * Установка в коллекцию элемента с соответствующим ключем
     *
     * @param   string $key
     * @param   mixed  $entity
     * @return  void
     */
    public function set(string $key, mixed $entity): void
    {
        $this->offsetSet($key, $entity);
    }

    /**
     * Проверка наличия элемента в коллекции с соответствующим ключём
     *
     * @param   string $key
     * @return  bool
     */
    public function __isset(string $key): bool
    {
        return $this->isset($key);
    }

    /**
     * Проверка наличия элемента в коллекции с соответствующим ключём
     *
     * @param   string $key
     * @return  bool
     */
    public function isset(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Удаление из коллекции элемента с соответствующим ключём
     *
     * @param   string $key
     * @return  void
     */
    public function __unset(string $key): void
    {
        $this->unset($key);
    }

    /**
     * Удаление из коллекции элемента с соответствующим ключём
     *
     * @param   string $key
     * @return  void
     */
    public function unset(string $key): void
    {
        $this->offsetUnset($key);
    }
}
