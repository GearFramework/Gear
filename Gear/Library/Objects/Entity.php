<?php

namespace Gear\Library\Objects;

use Gear\Interfaces\Objects\EntityInterface;
use Gear\Interfaces\Services\RepositoryInterface;
use Gear\Plugins\Templater\Viewer;
use Gear\Traits\Objects\EntityTrait;
use Serializable;

/**
 * Базовый класс объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Entity extends Model implements EntityInterface, Serializable
{
    /* Traits */
    use EntityTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected static array $config = [
        'plugins' => [
            'view' => ['class' => Viewer::class],
        ],
        'primaryKeyName' => 'id',
    ];
    /* Public */

    /**
     * Конструктор модели. На вход принимает массив свойств и объект, который является владельцем
     * данного объекта
     *
     * @param array                 $properties
     * @param EntityInterface|null  $owner
     */
    public function __construct(array $properties = [], ?EntityInterface $owner = null)
    {
        parent::__construct($properties, $owner);
    }

    /**
     * Серивализация объекта ввозовом функции serialize()
     *
     * @return array
     */
    public function __serialize(): array
    {
        return $this->props();
    }

    /**
     * Десериализация объекта вызовом функции unserialize()
     *
     * @param   array $properties
     * @return  void
     */
    public function __unserialize(array $properties): void
    {
        foreach ($properties as $nameProperty => $valueProperty) {
            $this->$nameProperty = $valueProperty;
        }
    }

    /**
     * Сериализация объекта
     *
     * @return string|null
     */
    public function serialize(): ?string
    {
        return serialize($this->props());
    }

    /**
     * Восстановление свойств объекта из строки
     *
     * @param   string|array $serialized
     * @return  void
     */
    public function unserialize(string|array $serialized): void
    {
        $properties = is_string($serialized) ? unserialize($serialized) : $serialized;
        if (is_array($properties) === false) {
            return;
        }
        foreach ($properties as $nameProperty => $valueProperty) {
            $this->$nameProperty = $valueProperty;
        }
    }

    public function getPrimaryKeyName(): string|array|null
    {
        return static::i('primaryKeyName');
    }

    public function getPrimaryKeyValue(bool $joined = false, string $separator = '-'): mixed
    {
        $primaryKeyName = static::i('primaryKeyName');
        return match ($primaryKeyName) {
            is_string($primaryKeyName)  => $this->props($primaryKeyName),
            is_array($primaryKeyName)   => $joined
                ? implode($separator, $this->props($primaryKeyName))
                : $this->props($primaryKeyName),
            default                     => null,
        };
    }

    public function getRepository(): ?RepositoryInterface
    {
        $owner = $this->getOwner();
        return $owner instanceof RepositoryInterface ? $owner : null;
    }
}
