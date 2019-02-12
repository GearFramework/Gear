<?php

namespace Gear\Interfaces;

/**
 * Интерфейс валидаторов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
interface GValidatorInterface
{
    /**
     * Валидация значения
     *
     * @param $value
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function validateValue($value): bool;

    /**
     * Валидация свойства объекта
     *
     * @param GObjectInterface $object
     * @param string $propertyName
     * @param mixed $defaultValue
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function validateProperty(GObjectInterface $object, string $propertyName, $defaultValue = null): bool;
}
