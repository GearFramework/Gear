<?php

namespace Gear\Library;

use Gear\Interfaces\ICache;

/**
 * Абстрактный класс для создания плагинов предоставляющих возможности доступа
 * к системам кэширования
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GCache extends GPlugin implements ICache
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_cache = null;
    protected $_serializer = 'serialize';
    protected $_unserializer = 'unserialize';
    /* Public */

    /**
     * Добавление нового значения в кэш
     *
     * @param string|array $key
     * @param mixed $value
     * @param integer $expire
     * @param null|\Closure|string|bool $serializer
     * @return array|bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function add($key, $value = null, $expire = 30, $serializer = null)
    {
        if (is_array($key)) {
            $args = func_get_args();
            $expire = isset($args[1]) ? (int)$args[1] : 30;
            if (isset($args[2])) {
                $serializer = is_callable($args[2]) ? $args[2] : ($args[2] === true ? $this->_serializer : false);
            } else {
                $serializer = false;
            }
            foreach ($key as $k => $v) {
                if ($serializer) {
                    $v = $serializer($v);
                }
                $result[] = $this->_add($k, $v, 0, $expire ? time() + $expire : 0);
            }
        } else {
            if ($serializer === true) {
                $value = call_user_func($this->_serializer, $value);
            } elseif (is_callable($serializer)) {
                $value = $serializer($value);
            }
            $result = $this->_add($key, $value, 0, $expire ? time() + $expire : 0);
        }
        return $result;
    }

    abstract protected function _add(string $key, $value, int $expire);

    /**
     * Очистка кэша
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    public function clear(): bool
    {
        return $this->_clear();
    }

    abstract protected function _clear(): bool;

    /**
     * Уменьшает значение в кэше на $step
     *
     * @param string|array $key
     * @param int $step
     * @return bool|int|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function dec($key, int $step = 1)
    {
        if (is_array($key)) {
            $result = [];
            foreach ($key as $k) {
                $result[$k] = $this->_dec($k, $step);
            }
        } else {
            $result = $this->_dec($key, $step);
        }
        return $result;
    }

    abstract protected function _dec(string $key, int $step);

    /**
     * Проверка на наличие в кэше значения под указанным ключём
     *
     * @param string|array $key
     * @return boolean|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exists($key)
    {
        if (is_array($key)) {
            $result = [];
            foreach ($key as $k) {
                $result[$k] = $this->_exists($k);
            }
        } else {
            $result = $this->_exists($key);
        }
        return $result;
    }

    abstract protected function _exists(string $key): bool;

    /**
     * Получение значения из кэша
     *
     * @param string|array $key
     * @param boolean|closure $unserializer
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function get($key, $unserializer = false)
    {
        if (is_array($key)) {
            $value = [];
            foreach ($key as $k => $uns) {
                if (is_bool($uns) || is_callable($uns))
                    $keyName = $k;
                else {
                    $keyName = $uns;
                    $uns = $unserializer;
                }
                if ($val = $this->_get($keyName)) {
                    if ($uns === true) {
                        $value[] = call_user_func($this->_unserializer, $val);
                    } elseif (is_callable($uns)) {
                        $value[] = $uns($val);
                    }
                    else {
                        $value[] = $val;
                    }
                }
            }
        } else {
            $value = $this->_get($key);
            if ($value && $unserializer) {
                $value = (is_callable($unserializer) ? $unserializer($value) : call_user_func($this->_unserializer, $value));
            }
        }
        return $value;
    }

    abstract protected function _get(string $key);

    /**
     * Увеличичвает значение в кэше на $step
     *
     * @param string|array $key
     * @param int $step
     * @return bool|int|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inc($key, int $step = 1)
    {
        if (is_array($key)) {
            $result = [];
            foreach ($key as $k) {
                $result[$k] = $this->_inc($k, $step);
            }
        } else {
            $result = $this->_inc($key, $step);
        }
        return $result;
    }

    abstract protected function _inc(string $key, int $step);

    /**
     * Удаление значения из кэша
     *
     * @param string|array $key
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($key)
    {
        if (is_array($key)) {
            $result = [];
            foreach ($key as $k) {
                $result[] = $this->_remove($k);
            }
        } else {
            $result = $this->_remove($key);
        }
        return $result;
    }

    abstract protected function _remove(string $key): bool;


    /**
     * Добавление значения или обновление существующего в
     * кэше
     *
     * @param string|array $key array as array(key => value, key => value, ...)
     * @param mixed $value as $expire when $key is array
     * @param integer $expire
     * @param boolean|callable $serializer
     * @return boolean|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function set($key, $value = null, $expire = 30, $serializer = false)
    {
        if (is_array($key)) {
            $args = func_get_args();
            $expire = isset($args[1]) ? (int)$args[1] : 30;
            if (isset($args[2])) {
                $serializer = is_callable($args[2]) ? $args[2] : ($args[2] === true ? $this->_serializer : false);
            } else {
                $serializer = false;
            }
            $result = [];
            foreach ($key as $k => $v) {
                if ($serializer) {
                    $v = $serializer($v);
                }
                $result[$k] = $this->_set($k, $v, 0, $expire ? time() + $expire : 0);
            }
        } else {
            if ($serializer === true) {
                $value = call_user_func($this->_serializer, $value);
            } elseif (is_callable($serializer)) {
                $value = $serializer($value);
            }
            $result = $this->_set($key, $value, 0, $expire ? time() + $expire : 0);
        }
        return $result;
    }

    abstract protected function _set(string $key, $value, int $expire): bool;
}
