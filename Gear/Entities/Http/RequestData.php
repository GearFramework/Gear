<?php

namespace Gear\Entities\Http;

use Gear\Interfaces\Http\RequestDataInterface;
use Gear\Library\Objects\Entity;

/**
 * Модель с данными запроса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class RequestData extends Entity implements RequestDataInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected array $validates = [];
    /* Public */

    public function __get(string $name): mixed
    {
        $method = 'get' . ucfirst($name);
        $value = method_exists($this, $method) ? $this->{$method}() : $this->props($name);
        return $this->validate($name, $value);
    }

    public function param($name, $default = null)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }
        return $default;
    }

    /**
     * Валидация значения
     *
     * @param   string|null   $name
     * @param   mixed         $value
     * @param   mixed         $default
     * @param   callable|null $validator
     * @return  mixed
     */
    public function validate(
        string $name = null,
        mixed $value = null,
        mixed $default = null,
        callable $validator = null
    ): mixed {
        if (is_callable($validator)) {
            return $validator($value, $default);
        }
        if (isset($this->validates[$name]) === false) {
            return $value;
        }
        $validateMethod = $this->validates[$name];
        if (is_string($validateMethod) && method_exists($this, $validateMethod)) {
            return $this->$validateMethod($value, $default);
        }
        if (is_array($validateMethod)) {
            foreach ($validateMethod as $method) {
                if (method_exists($this, $method) === false) {
                    continue;
                }
                $value = $this->$method($value);
                if ($value === $default) {
                    break;
                }
            }
        }
        return $value;
    }
}
