<?php

namespace gear\modules\resource\plugins;
use gear\Core;
use gear\library\GPlugin;

/** 
 * Каркас для ресурсов типа javascript, css 
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 28.01.2014
 * @php 5.4.x or higher
 * @release 1.0.0
 */
abstract class GClientResource extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    /**
     * @var string $_mappingFolder mapping folder for resources in DOCUMENT_ROOT
     */
    protected $_mappingFolder = '';
    /* Public */
    public $html = '';
    public $url = '';
    public $path = '';
    public $temp = 'temp\resources';

    /**
     * Возрвщает название папки, в которую отражаются файлы ресурсов
     *
     * @access public
     * @param string $folder
     * @return $this
     */
    public function getMappingFolder() {
        return $this->_mappingFolder;
    }

    /**
     * Установка папки, в которую будут отражаться файлы ресурсов
     *
     * @access public
     * @param string $folder
     * @return $this
     */
    public function setMappingFolder($folder) {
        $this->_mappingFolder = $folder;
        return $this;
    }

    /**
     * Возвращает расширение ресурса
     *
     * @access public
     * @return string
     */
    public function getExtensionResource() {
        return $this->_extension;
    }

    /**
     * Получение mime-тип ресурса
     *
     * @access public
     * @return string
     */
    public function getContentType() {
        return $this->_contentType;
    }

    /**
     * Публикация ресурса (ввиде ссылки). Параметр $render установленный в true
     * позволяет провести предварительный рендеринг ресурса в шаблонизаторе,
     * таким образом ресурс может быть динамическим и содержать php-код
     * Параметр $mapping, установленный в true копирует скрипт в указанную
     * папку в DOCUMENT_ROOT
     *
     * @access public
     * @param string $resource
     *
     * @param boolean $render
     * @param boolean $mapping
     * @return string
     */
    public function publicate($resource, $render = false, $mapping = null) {
        if (!preg_match('/[\/|\\\\]/', $resource))
            $resource = $this->resourcesPath . '\\' . $this->path . '\\' . $resource;
        Core::syslog(__CLASS__ . ' -> Publicate resource ' . $resource . '[' . __LINE__ . ']');
        $resourcePath = Core::resolvePath($resource);
        Core::syslog(__CLASS__ . ' -> Resource path ' . $resourcePath . '[' . __LINE__ . ']');
        $hash = md5($resourcePath);
        Core::syslog(__CLASS__ . ' -> Resource hash ' . $hash . '[' . __LINE__ . ']');
        die();
        if ($render) {
            Core::syslog(__CLASS__ . ' -> Rendering resource [' . __LINE__ . ']');
            $content = $this->owner->view->render($resourcePath, array(), true);
        }
        if ($this->mappingFolder && $mapping !== false) {
            $file = Core::app()->env->DOCUMENT_ROOT . '/' . $this->mappingFolder . '/' . $hash . '.' . $this->getExtensionResource();
            Core::syslog(__CLASS__ . ' -> Mapping resource to ' . $file . ' [' . __LINE__ . ']');
            die();
            if (!file_exists($file) || $render)
                file_put_contents($file, $render ? $content : file_get_contents($resourcePath));
            $url = $this->mappingFolder . '/' . $hash . '.' . $this->getExtensionResource();
        } else {
            Core::syslog(__CLASS__ . ' -> Use temp resource [' . __LINE__ . ']');
            die();
            if ($this->useCache) {
                if (!$this->cache->exists($hash) || $render)
                    $this->cache->add($hash, $render ? $content : file_get_contents($resourcePath));
            } else {
                $file = Core::resolvePath($this->temp . '\\' . $hash . '.' . $this->getExtensionResource());
                if (!file_exists($file) || $render)
                    file_put_contents($file, $render ? $content : file_get_contents($resourcePath));
            }
            $url = $this->url;
        }
        return $this->getHtml($hash, $url);
    }

    /**
     * Возвращает контент ресурса
     *
     * @access public
     * @param string $hash
     * @return string
     */
    public function get($hash) {
        header('Content-Type: ' . $this->getContentType());
        if ($this->useCache)
            echo $this->cache->get($hash);
        else {
            $file = Core::resolvePath($this->temp . '\\' . $hash . '.' . $this->getExtensionResource());
            echo file_exists($file) ? file_get_contents($file) : '';
        }
        return true;
    }

    /**
     * Возвращает подготовленную html-строку для публикации на странице
     *
     * @access private
     * @param string $hash
     * @return string
     */
    abstract public function getHtml($hash, $url = null, $params = []);

    /**
     * Генерация ключа для ресурса
     *
     * @access public
     * @param string $file
     * @return string
     */
    public function getHash($file) { return md5($file); }
}
