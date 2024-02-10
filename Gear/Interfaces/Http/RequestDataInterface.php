<?php


namespace Gear\Interfaces\Http;

/**
 * Интерфейс модели с данными запроса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface RequestDataInterface
{
    /**
     * Валидация значения
     *
     * @param string|null   $name
     * @param mixed         $value
     * @param mixed         $default
     * @param callable|null $validator
     * @return mixed
     */
    public function validate(
        string $name = null,
        mixed $value = null,
        mixed $default = null,
        callable $validator = null
    ): mixed;
}
