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
use gear\interfaces\ILoader;

/** 
 * Класс стандартного компонента, занимающегося автозагрузкой классов
 * 
 * @package Gear Framework
 * @component Loader
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 02.08.2013
 * @php 5.3.x
 */
class GLoader extends GComponent implements ILoader
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array('autoloadHandler' => 'loader');
    protected static $_init = false;
    /* Public */
    public $aliases = array();
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
        if (isset($this->aliases[$className]))
        {
            $alias = $className;
            list($className) = Core::getRecords($this->aliases[$className]);
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
            if (preg_match('/^[a-zA-Z]{1}\:/', $namespace) || substr($namespace, 0, 1) == '/')
                $path = $namespace;
            else
            {
                if (substr($namespace, 0, 1) == '\\')
                    $path = GEAR . '/..' . str_replace('\\', '/', $namespace);
                else
                    $path = GEAR . '/../' . (Core::isModuleInstalled('app') ? Core::app()->getNamespace() . '/' : 'gear/') . str_replace('\\', '/', $namespace);
            }
        }
        return $path;
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
