<?php

namespace gear\plugins\gear\cache;
use gear\Core;
use gear\library\cache\GCache;
use gear\library\GModel;

/** 
 * Плагин для Memcache
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 29.01.2014
 */
class GMemCache extends GCache
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_servers = null;
    /* Public */
    public $defaultHost = '127.0.0.1';
    public $defaultPort = 11211;
    
    /**
     * Установка списка серверов
     * 
     * @access public
     * @param array $servers
     * @return void
     */
    public function addServers($servers)
    {
        foreach($servers as $server)
            $this->_servers[] = new GModel($server);
    }
    
    /**
     * Возвращает список серверов
     * 
     * @access public
     * @return array
     */
    public function getServers()
    {
        return $this->_servers;
    }
    
    /**
     * Добавление значения в кэш
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function add($key, $value, $expire = 30)
    {
    }
    
    /**
     * Добавление значения или обновление существующего в 
     * кэше
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function set($key, $value, $expire = 30)
    {
    }
    
    /**
     * Получение значения из кэша
     * 
     * @abstract
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function get($key, $unserialize = false)
    {
    }
    
    /**
     * Проверка на наличие в кэше значения под указанным ключём
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    public function exists($key)
    {
    }
    
    /**
     * Удаление значения из кэша
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    public function remove($key)
    {
    }
    
    /**
     * Очистка кэша
     * 
     * @abstract
     * @access public
     * @return boolean
     */
    public function clear()
    {
    }
    
    /**
     * Обработчик события конструктора класса
     * 
     * @access public
     * @return boolean
     */
    public function onConstructed()
    {
        parent::onConstructed();
        $this->_cache = new \Memcache();
        if ($this->_servers)
        {
            foreach($this->_servers as $server)
                $this->_cache->addServer($server->host, $server->port);
        }
        else
            $this->_cache->addServer($this->defaultHost, $this->defaultPort);
        return true;
    }
}
