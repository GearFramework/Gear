<?php

namespace Gear\Library\Io;

use Gear\Interfaces\FileSystemOptionsInterface;
use Gear\Interfaces\IoInterface;
use Gear\Interfaces\StaticFactoryInterface;
use Gear\Library\Model;
use Gear\Traits\Factory\StaticFactoryTrait;

/**
 * Класс ввода-вывода
 *
 * @package Gear Framework 2
 *
 * @property resource handler
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
abstract class Io extends Model implements IoInterface
{
    /* Traits */
    use StaticFactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    /** @var null|resource $handler */
    protected $handler = null;
    /* Public */

    /**
     * При $type равным
     * NULL - возвращает тип элемента соответствующее одному из значений
     *        FileSystem::FILE|FileSystem::DIRECTORY|FileSystem::LINK
     * целочисленное значение
     * целое число - возвращает
     *        true или false при соответствии
     * строковое значение - возвращает true или false при
     *        соответствии
     *
     * @param null|int|string $type
     * @return string|int|bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function isA(null|int|string $type = null): string|int|bool
    {
        return match ($type) {
            is_numeric($type)   => array_search($this->type(), self::IO_TYPES) === (int)$type,
            is_string($type)    => $this->type() === $type,
            null                => array_search($this->type(), self::IO_TYPES),
            default             => self::UNKNOWN,
        };
    }

    /**
     * Возвращает строковое значение соответствующее типу элемента
     *
     * @return false|string
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function type(): false|string;
}
