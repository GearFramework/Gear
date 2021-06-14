<?php

namespace Gear\Models\Http;

use Gear\Interfaces\RequestDataInterface;
use Gear\Library\GModel;

/**
 * Модель с данными запроса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class GRequestData extends GModel implements RequestDataInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_validates = [];
    /* Public */

    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);
        $value = method_exists($this, $method) ? $this->{$method}() : $this->props($name);
        return $this->validate($name, $value);
    }

    public function param($name, $default = null)
    {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        } else {
            return $default;
        }
    }

    /**
     * Валидация значения
     *
     * @param string $name
     * @param string|null $value
     * @param mixed $default
     * @param mixed $validator
     * @return mixed|string|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function validate($name = null, $value = null, $default = null, $validator = null)
    {
        if (isset($this->_validates[$name])) {
            if (is_string($this->_validates[$name]) && method_exists($this, $this->_validates[$name])) {
                $validateMethod = $this->_validates[$name];
                $value = $this->$validateMethod($value);
            } elseif (is_array($this->_validates[$name])) {
                foreach ($this->_validates[$name] as $validateMethod) {
                    if (method_exists($this, $validateMethod)) {
                        $value = $this->$validateMethod($value);
                        if ($value === null) {
                            break;
                        }
                    }
                }
            }
        }
        return $value;
    }
}
