<?php

namespace gear\modules\resource;
use \gear\Core;
use \gear\library\GModule;
use \gear\library\GEvent;
use \gear\library\GException;

/** 
 * Модуль для работы с ресурсами 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 28.01.2014
 */
class GResource extends GModule
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'plugins' => array
        (
            'js' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GJsResource'
            ),
            'css' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GCssResource'
            ),
            'cache' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GResourceCache',
            )
        ),
        'salt' => 'Rui43VbthF#',
    );
    /* Public */
    public $storage = 'resources';  // Пусть к папке, где лежат ресурсы
    
    /**
     * Запрос на публикацию ресурса
     * 
     * @access public
     * @param string $file
     * @param string $wrapper
     * @param boolean $render
     * @return boolean
     */
    public function publicate($file, $wrapper = null, $render = false)
    {
        if (!$wrapper)
            $wrapper = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return $this->p($wrapper)->publicate($file, $render);
    }
    
    /**
     * Получение содержимого ресурса
     * 
     * @access public
     * @param string $hash
     * @param string $wrapper
     * @return mixed
     */
    public function get($hash, $wrapper)
    {
        return $this->p($wrapper)->get($hash);
    }
    
    /**
     * Получение mime-тип ресурса
     * 
     * @access public
     * @param string $wrapper
     * @return string
     */
    public function getContentType($wrapper)
    {
        return $this->p($wrapper)->getContentType();
    }
    
    /**
     * Кэширование данных о ресурсе
     * 
     * @access public
     * @param string $file
     * @param array $params
     * @return null|string of md5-hash
     */
    public function cache($file, array $params = array())
    {
        $hash = $this->getHash($file);
        $params['resource'] = $file;
        return $this->cache->set($hash, $params) ? $hash : null;
    }
    
    /**
     * Проверяет, существует ли в кэше информация о ресурсе
     * 
     * @access public
     * @param string $hash
     * @return boolean
     */
    public function inCache($hash)
    {
        return $this->cache->exists($hash) ? true : false;
    }
    
    /**
     * Генерация ключа для ресурса
     * 
     * @access public
     * @param string $file
     * @return string
     */
    public function getHash($file)
    {
        return md5($file . $this->i('salt'));
    }
}
