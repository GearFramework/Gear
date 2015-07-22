<?php

/**
 * Файл с реализацией компонента стандартного загрузчика
 * классов
 * PHP 5.3.x и выше
 */

namespace gear\components\gear\loader;
use gear\Core;
use gear\library\GComponent;
use gear\library\GException;
use gear\library\GEvent;
use gear\interfaces\ILoader;

/** 
 * Класс стандартного компонента, занимающегося автозагрузкой классов
 * 
 * @package Gear Framework
 * @component Loader
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 02.08.2013
 * @php 5.3.x
 * @release 1.0.0
 */
class GLoader extends GComponent implements ILoader
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array('autoloadHandler' => 'loader');
    protected static $_init = false;
    protected $_aliases = array();
    /* Public */
    public $usePaths = false;
    public $paths = array();
    public $useResolvePaths = false;
    public $resolvePaths = array();
    
    /**
     * Метод автоматической загрузки классов
     * - Поддержка алиасов
     * - Поддержка пользовательских путей расположения файлов с классами
     * 
     * @access public
     * @param string $className
     * @return void
     */
    public function loader($className)
    {
        if (isset($this->_aliases[$className]))
        {
            $alias = $className;
            list($className) = Core::getRecords($this->_aliases[$className]);
            class_alias($className, $alias);
        }
        if ($this->usePaths && isset($this->paths[$className]))
            $file = $this->paths[$className];
        else
            $file = GEAR . '/../' . str_replace('\\', '/', $className) . '.php';
        if (!file_exists($file))
            $this->e('Library ":library" of class ":className" not found', array('library' => $file, 'className' => $className));
        include_once($file);
    }
    
    /**
     * Получение физического пути.
     * 
     * @access public
     * @param string $namespace
     * @return string
     */
    public function resolvePath($namespace)
    {
        $path = false;
        if ($this->useResolvePaths && isset($this->resolvePaths[$namespace]))
            $path = $this->resolvePaths[$namespace];
        else
        {
            /* Абсолютный путь */
            if (preg_match('/^[a-zA-Z]{1}\:/', $namespace) || $namespace[0] === '/')
                $path = $namespace;
            /* Относительный путь или пространство имён */
            else
            {
                $path = GEAR . '/../';
                if ($namespace[0] !== '\\')
                    $path .= (Core::isModuleInstalled('app') ? Core::app()->getNamespace() . '/' : 'gear/');
                $path .= str_replace('\\', '/', $namespace);
            }
        }
        return $path;
    }

    /**
     * Установка алиаса для класса
     *
     * @access public
     * @param string $className
     * @param string $alias
     * @return $this
     */
    public function setAlias($className, $alias)
    {
        $this->_aliases[$alias] = array('class' => $className);
        return $this;
    }

    /**
     * Устанавливает новый список алиасов
     *
     * @access public
     * @param array $aliases
     * @return $this
     */
    public function setAliases(array $aliases)
    {
        $this->_aliases = $aliases;
        return $this;
    }

    /**
     * Возвращает список алиасов
     *
     * @access public
     * @return array
     */
    public function getAliases() { return $this->_aliases; }

    /**
     * Добавление спсика алиасов к существующим
     *
     * @access public
     * @param array $aliases
     * @return $this
     */
    public function appendAliases(array $aliases)
    {
        $this->aliases = array_merge($this->aliases, $aliases);
        return $this;
    }
    
    /**
     * Обработчик события onInstalled по-умолчанию
     * Регистрация метода автозагрузки классов
     * 
     * @access public
     * @param GEvent $event
     * @return void
     */
    public function onInstalled($event)
    {
        if (!($handlerName = $this->i('autoloadHandler')))
            $this->e('Not specified "autoloadHandler"');
        spl_autoload_register(array($this, $handlerName));
        return true;
    }
}

/** 
 * Класс исключений компонента автозагрузки классов
 * 
 * @package Gear Framework
 * @component Loader
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 26.04.2013
 */
class LoaderException extends GException 
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
