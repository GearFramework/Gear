<?php

namespace gear\interfaces;

defined('DEFAULT_CACHE_EXPIRE') or define('DEFAULT_CACHE_EXPIRE', 30);

/**
 * Интерфейс кэша
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface ICache
{
    /**
     * Добавление нового значения в кэш
     *
     * @param string|array $key
     * @param mixed $value
     * @param integer $expire
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function add($key, $value, $expire = DEFAULT_CACHE_EXPIRE);

    /**
     * Очистка кэша
     *
     * @access public
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function clear();

    /**
     * Уменьшает значение в кэше на $step
     *
     * @param string|array $key
     * @param int $step
     * @return bool|int|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function dec($key, int $step = 1);

    /**
     * Проверка на наличие в кэше значения под указанным ключём
     *
     * @access public
     * @param string|array $key
     * @return boolean|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exists($key);

    /**
     * Получение значения из кэша
     *
     * @access public
     * @param string|array $key
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function get($key);

    /**
     * Увеличичвает значение в кэше на $step
     *
     * @param string|array $key
     * @param int $step
     * @return bool|int|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inc($key, int $step = 1);

    /**
     * Удаление значения из кэша
     *
     * @access public
     * @param string|key $key
     * @return boolean|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($key);

    /**
     * Добавление нового значения в кэш или обновление существующего
     *
     * @param string|array $key
     * @param mixed $value
     * @param integer $expire
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function set($key, $value, $expire = DEFAULT_CACHE_EXPIRE);
}
