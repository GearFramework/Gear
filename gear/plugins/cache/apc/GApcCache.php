<?php

namespace gear\plugins\cache\apc;

use gear\interfaces\ICache;
use gear\plugins\cache\GCache;

/**
 * Плагин для APC-кэша
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GApcCache extends GCache implements ICache
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Добавление нового значения в кэш
     *
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _add(string $key, $value, int $expire): bool
    {
        return apc_add($key, $value, $expire);
    }

    /**
     * Очистка кэша
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _clear(): bool
    {
        return apc_clear_cache();
    }

    /**
     * Уменьшает значение в кэше на $step
     *
     * @param string $key
     * @param integer $step
     * @return int|bool
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _dec(string $key, int $step)
    {
        return apc_dec($key, $step);
    }

    /**
     * Проверка на наличие в кэше значения под указанным ключём
     *
     * @param string $key
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _exists(string $key): bool
    {
        return apc_exists($key);
    }

    /**
     * Получение значения из кэша
     *
     * @param string $key
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _get(string $key)
    {
        return apc_fetch($key);
    }

    /**
     * Увеличичвает значение в кэше на $step
     *
     * @param string $key
     * @param integer $step
     * @return int|bool
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _inc(string $key, int $step)
    {
        return apc_inc($key, $step);
    }

    /**
     * Удаление значения из кэша
     *
     * @param string $key
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _remove(string $key): bool
    {
        return apc_delete($key);
    }

    /**
     * Добавление значения или обновление существующего в
     * кэше
     *
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _set(string $key, $value, int $expire): bool
    {
        return apc_store($key, $value, $expire);
    }
}
