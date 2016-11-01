<?php

namespace gear\plugins\gear\cache;

use gear\Core;
use gear\library\GModel;
use gear\library\cache\GCache;

/**
 * Плагин для Memcache
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @license MIT
 * @since 28.06.2016
 * @version 1.0.0
 */
class GMemcache extends GCache
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_servers = [];
    protected $_serializer = 'serialize';
    protected $_unserializer = 'unserialize';
    protected $_defaultHost = '127.0.0.1';
    protected $_defaultPort = 11211;
    /* Public */

    /**
     * Установка списка серверов
     *
     * @access public
     * @return array
     * @since 1.0.0
     */
    public function setServers(array $servers)
    {
        $this->_servers = $servers;
    }

    /**
     * Возвращает список серверов
     *
     * @access public
     * @return array
     * @since 1.0.0
     */
    public function getServers()
    {
        return $this->_servers;
    }

    /**
     * Установка списка серверов
     *
     * @access public
     * @param array $servers
     * @since 1.0.0
     */
    public function addServers(array $servers)
    {
        foreach ($servers as $server)
            $this->_servers[] = new GModel($server);
    }

    /**
     * Добавление сервера
     *
     * @access public
     * @param array|object $server
     * @throws \CacheException
     * @since 1.0.0
     */
    public function addServer($server)
    {
        if (is_array($server))
            $server = new GModel($server);
        if (!($server instanceof GModel))
            throw $this->exceptionCacheInvalidServer();
        $this->_servers[] = $server;
    }

    /**
     * Добавление значения в кэш
     *
     * @access public
     * @param string|array $key array as array(key => value, key => value, ...)
     * @param mixed $value as $expire when $key is array
     * @param integer $expire
     * @return boolean
     * @since 1.0.0
     */
    public function add($key, $value = null, $expire = 30, $serializer = false)
    {
        if (is_array($key)) {
            $args = func_get_args();
            $expire = isset($args[1]) ? (int)$args[1] : 30;
            if (isset($args[2]))
                $serializer = is_callable($args[2]) ? $args[2] : ($args[2] === true ? $this->_serializer : false);
            else
                $serializer = false;
            $size = $result = count($key);
            foreach ($key as $k => $v) {
                if ($serializer)
                    $v = $serializer($v);
                $result = $this->_cache->add($k, $v, 0, $expire ? time() + $expire : 0) ? $result - 1 : $result;
            }
            return !$result ? true : ($size === $result ? false : $result);
        }
        if ($serializer === true)
            $value = call_user_func($this->_serializer, $value);
        else
            if (is_callable($serializer))
                $value = $serializer($value);
        return $this->_cache->add($key, $value, 0, $expire ? time() + $expire : 0);
    }

    /**
     * Добавление значения или обновление существующего в
     * кэше
     *
     * @access public
     * @param string|array $key array as array(key => value, key => value, ...)
     * @param mixed $value as $expire when $key is array
     * @param integer $expire
     * @param boolean|callable $serializer
     * @return boolean
     * @since 1.0.0
     */
    public function set($key, $value = null, $expire = 30, $serializer = false)
    {
        if (is_array($key)) {
            $args = func_get_args();
            $expire = isset($args[1]) ? (int)$args[1] : 30;
            if (isset($args[2]))
                $serializer = is_callable($args[2]) ? $args[2] : ($args[2] === true ? $this->_serializer : false);
            else
                $serializer = false;
            $size = $result = count($key);
            foreach ($key as $k => $v) {
                if ($serializer)
                    $v = $serializer($v);
                $result = $this->_cache->set($k, $v, 0, $expire ? time() + $expire : 0) ? $result - 1 : $result;
            }
            return !$result ? true : ($size === $result ? false : $result);
        }
        if ($serializer === true)
            $value = call_user_func($this->_serializer, $value);
        else if (is_callable($serializer))
            $value = $serializer($value);
        return $this->_cache->set($key, $value, 0, $expire ? time() + $expire : 0);
    }

    /**
     * Получение значения из кэша
     *
     * @access public
     * @param string|array $key
     * @param boolean|closure $unserializer
     * @return mixed
     * @since 1.0.0
     */
    public function get($key, $unserializer = false)
    {
        if (is_array($key)) {
            $result = array();
            foreach ($key as $k => $uns) {
                if (is_bool($uns) || is_callable($uns))
                    $keyName = $k;
                else {
                    $keyName = $uns;
                    $uns = $unserializer;
                }
                if ($value = $this->_cache->get($keyName)) {
                    if ($uns === true)
                        $result[] = call_user_func($this->_unserializer, $value);
                    else if (is_callable($uns))
                        $result[] = $uns($value);
                    else
                        $result[] = $value;
                }
            }
            return $result;
        }
        $value = $this->_cache->get($key);
        if ($value && $unserializer)
            $value = !$unserializer ? $value : (is_callable($unserializer) ? $unserializer($value) : call_user_func($this->_unserializer, $value));
        return $value;
    }

    /**
     * Проверка на наличие в кэше значения под указанным ключём
     *
     * @access public
     * @param string|array $key
     * @return boolean|array
     * @since 1.0.0
     */
    public function exists($key)
    {
        if (is_array($key)) {
            $result = array();
            foreach ($key as $k)
                $result[$k] = $this->_cache->get($k) !== false ? true : false;
        }
        return $this->_cache->get($key) !== false ? true : false;
    }

    /**
     * Удаление значения из кэша
     *
     * @access public
     * @param string|array $key
     * @return boolean
     * @since 1.0.0
     */
    public function remove($key)
    {
        if (is_array($key)) {
            $size = $result = count($key);
            foreach ($key as $k)
                $result = $this->_cache->delete($k) ? $result - 1 : $result;
            return !$result ? true : ($result === $size ? false : $result);
        }
        return $this->_cache->delete($key);
    }

    /**
     * Очистка кэша
     *
     * @access public
     * @return boolean
     * @since 1.0.0
     */
    public function clear()
    {
        return $this->_cache->flush();
    }

    /**
     * Увеличичвает значение в кэше на $step
     *
     * @access public
     * @param string $key
     * @param integer $step
     * @return boolean
     * @since 1.0.0
     */
    public function inc($key, $step = 1)
    {
        return $this->_cache->increment($key, $step);
    }

    /**
     * Уменьшает значение в кэше на $step
     *
     * @access public
     * @param string $key
     * @param integer $step
     * @return boolean
     * @since 1.0.0
     */
    public function dec($key, $step = 1)
    {
        return $this->_cache->decrement($key, $step);
    }

    /**
     * Обработчик события конструктора класса
     *
     * @access public
     * @return boolean
     * @since 1.0.0
     */
    public function onAfterConstruct()
    {
        $this->_cache = new \Memcache();
        if ($this->_servers) {
            foreach ($this->_servers as $server)
                $this->_cache->addServer($server->host, $server->port);
        } else
            $this->_cache->addServer($this->defaultHost, $this->defaultPort);
        return true;
    }
}
