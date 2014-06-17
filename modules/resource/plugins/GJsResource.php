<?php

namespace gear\modules\resource\plugins;
use gear\Core;
use gear\modules\resource\plugins\GClientResource;

/** 
 * Плагин для работы с javascript-ресурсами 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 28.01.2014
 */
class GJsResource extends GClientResource
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $html = "<script type=\"%s\" src=\"%s\"></script>\n";
    public $url = '?e=gear/resource/get&f=client&hash=js:%s';
    public $path = 'js'; 
    public $temp = 'temp/resources/js';
    
    /**
     * Возвращает подготовленную html-строку для публикации на странице
     * 
     * @access private
     * @param string $hash
     * @return string
     */
    private function _getHtml($hash)
    {
        return sprintf($this->html, $this->getContentType(), sprintf($this->url, $hash));
    }
    
    /**
     * Публикация ресурса (ввиде ссылки). Параметр $render установленный в true
     * позволяет провести предварительный рендеринг ресурса в шаблонизаторе,
     * таким образом ресурс может быть динамическим и содержать php-код 
     * 
     * @access public
     * @param string $resource
     * @param boolean $render
     * @return string
     */
    public function publicate($resource, $render = false)
    {
        if (!preg_match('/[\/|\\\\]/', $resource))
            $resource = $this->resourcesPath . '\\' . $this->path . '\\' . $resource; 
        $resourcePath = Core::resolvePath($resource);
        $hash = $this->getHash($resourcePath);
        if ($render)
            $content = $this->owner->view->render($resourcePath, array(), true);
        if ($this->useCache)
        {
            if (!$this->cache->exists($hash) || $render)
                $this->cache->add($hash, $render ? $content : file_get_contents($resourcePath));
        }
        else
        {
            $file = Core::resolvePath($this->temp . '\\' . $hash . '.js');
            if (!file_exists($file) || $render)
                file_put_contents($file, $render ? $content : file_get_contents($resourcePath));
        }
        return $this->_getHtml($hash);
    }
    
    /**
     * Возвращает контент ресурса
     * 
     * @access public
     * @param string $hash
     * @return string
     */
    public function get($hash)
    {
        header('Content-Type: ' . $this->getContentType());
        if ($this->useCache)
            echo $this->cache->get($hash);
        else
        {
            $file = Core::resolvePath($this->temp . '\\' . $hash . '.js');
            echo file_exists($file) ? file_get_contents($file) : '';
        }
        return true;
    }
    
    /**
     * Получение mime-тип ресурса
     * 
     * @access public
     * @return string
     */
    public function getContentType()
    {
        return 'text/javascript';
    }
}
