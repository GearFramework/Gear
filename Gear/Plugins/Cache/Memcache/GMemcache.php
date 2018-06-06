<?php

namespace Gear\Plugins\Cache\Memcache;

use Gear\Library\GCache;

/**
 * Плагин для Memcache
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GMemcache extends GCache
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_cache = null;
    protected $_servers = [];
    protected $_defaultHost = '127.0.0.1';
    protected $_defaultPort = 11211;
    protected $_isMemcached = false;
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
    protected function _add(string $key, $value, int $expire)
    {
        return $this->_cache->add($key, $value, 0, $expire);
    }

    /**
     * Добавление сервера
     *
     * @param array|object $server
     * @throws \CacheInvalidServerException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function addServer($server)
    {
        if (is_array($server))
            $server = new GModel($server);
        if (!($server instanceof GModel))
            throw static::exceptionCacheInvalidServer();
        $this->_servers[] = $server;
    }

    /**
     * Установка списка серверов
     *
     * @param array $servers
     * @since 0.0.1
     * @version 0.0.1
     */
    public function addServers(array $servers)
    {
        foreach ($servers as $server)
            $this->_servers[] = new GModel($server);
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
        return $this->_cache->flush();
    }

    /**
     * Уменьшает значение в кэше на $step
     *
     * @param string $key
     * @param int $step
     * @return bool|int
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _dec(string $key, int $step)
    {
        return $this->_cache->decrement($key, $step);
    }

    /**
     * Проверка на наличие в кэше значения под указанным ключём
     *
     * @param string $key
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    public function _exists(string $key): bool 
    {
        return $this->_cache->get($key) !== false ? true : false;
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
        return $this->_get($key);
    }

    /**
     * Возвращает true, если используется Memcached
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIsMemcached(): bool
    {
        return $this->_isMemcached;
    }

    /**
     * Возвращает список серверов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getServers(): array
    {
        return $this->_servers;
    }

    /**
     * Увеличичвает значение в кэше на $step
     *
     * @param string|array $key
     * @param int $step
     * @return bool|int
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _inc(string $key, int $step)
    {
        return $this->_cache->increment($key, $step);
    }

    /**
     * Удаление значения из кэша
     *
     * @param string|array $key
     * @return bool|array
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _remove(string $key): bool
    {
        return $this->_cache->delete($key);
    }

    /**
     * Добавление значения или обновление существующего в
     * кэше
     *
     * @param string|array $key array as array(key => value, key => value, ...)
     * @param mixed $value as $expire when $key is array
     * @param int $expire
     * @return bool
     * @since 1.0.0
     */
    protected function _set(string $key, $value, int $expire): bool
    {
        return $this->_cache->set($key, $value, 0, $expire);
    }

    /**
     * Устанавливает будет ли использоваться Memcached или нет
     *
     * @param bool $isMemcached
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setIsMemcached(bool $isMemcached)
    {
        $this->_isMemcached = $isMemcached;
    }

    /**
     * Установка списка серверов
     *
     * @param array $servers
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setServers(array $servers)
    {
        $this->_servers = $servers;
    }

    /**
     * Обработчик события конструктора класса
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    public function onAfterConstruct()
    {
        $this->_cache = $this->isMemcached ? new \Memcached() : new \Memcache();
        if ($this->_servers) {
            foreach ($this->_servers as $server) {
                $this->_cache->addServer($server->host, $server->port);
            }
        } else {
            $this->_cache->addServer($this->defaultHost, $this->defaultPort);
        }
        return true;
    }
}
