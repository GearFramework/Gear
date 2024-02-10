<?php

namespace Gear\Interfaces\Objects;

/**
 * Интерфейс объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface EntityInterface extends ModelInterface
{

    public function getPrimaryKeyName(): string|array|null;

    public function getPrimaryKeyValue(bool $joined = false): mixed;
}
