<?php

/**
 * Файл с реализацией компонента стандартного загрузчика
 * классов
 * PHP 5.4.x и выше
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
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GLoader extends GComponent implements ILoader
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = ['autoloadHandler' => 'loader'];
    protected static $_init = false;
    protected $_aliases = [];
    /* Public */
    public $usePaths = false;
    public $paths = [];
    public $useResolvePaths = false;
    public $resolvePaths = [];

    /**
     * Метод автоматической загрузки классов
     * - Поддержка алиасов
     * - Поддержка пользовательских путей расположения файлов с классами
     *
     * @access public
     * @param string $className
     * @return void
     */
    public function loader($className) {
        Core::syslog(__CLASS__ . ' -> Load class ' . $className . ' [' . __LINE__ . ']');
        if (isset($this->_aliases[$className]['class'])) {
            $alias = $className;
            $className = $this->_aliases[$className]['class'];
            class_alias($className, $alias);
        }
        if ($this->usePaths && isset($this->paths[$className]))
            $file = $this->paths[$className];
        else
            $file = GEAR . '/../' . str_replace('\\', '/', $className) . '.php';
        Core::syslog(__CLASS__ . ' -> Resolve ' . $className . ' and load as ' . $file . ' [' . __LINE__ . ']');
        if (!file_exists($file))
            throw $this->exceptionLoaderClassFileNotFound(['filename' => $file, 'className' => $className]);
        include_once($file);
    }

    /**
     * Получение физического пути.
     *
     * @access public
     * @param string $namespace
     * @return string
     */
    public function resolvePath($namespace) {
        if ($this->useResolvePaths && isset($this->resolvePaths[$namespace])) {
            $path = $this->resolvePaths[$namespace];
            Core::syslog(__CLASS__ . ' -> Resolve ' . $namespace . ' as ' . $path . ' from resolvePaths array [' . __LINE__ . ']');
        } else {
            /* Абсолютный путь */
            if (preg_match('/^[a-zA-Z]{1}\:/', $namespace) || $namespace[0] === '/') {
                $path = $namespace;
                Core::syslog(__CLASS__ . ' -> Resolve ' . $namespace . ' as ' . $path . ' from absolute path [' . __LINE__ . ']');
            } else {
                /* Относительный путь или пространство имён */
                $path = GEAR . '/../';
                if ($namespace[0] !== '\\')
                    $path .= (Core::isModuleInstalled('app') ? Core::app()->getNamespace() . '/' : 'gear/');
                $path .= $namespace;
                $path = str_replace('\\', '/', $path);
                Core::syslog(__CLASS__ . ' -> Resolve ' . $namespace . ' as ' . $path . ' from relative path [' . __LINE__ . ']');
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
    public function setAlias($className, $alias) {
        Core::syslog(get_class($this) . ' -> Set alias ' . $alias . ' for class ' . $className . ' [' . __LINE__ . ']');
        $this->_aliases[$alias] = ['class' => $className];
        return $this;
    }

    /**
     * Возвращает оригинальное название класса, которому соответствует указанный алиас
     *
     * @access public
     * @param string $alias
     * @return null|string
     */
    public function getAlias($alias) {
        return isset($this->_aliases[$alias]['class']) ? $this->_aliases[$alias]['class'] : null;
    }

    /**
     * Устанавливает новый список алиасов
     *
     * @access public
     * @param array $aliases
     * @return $this
     */
    public function setAliases(array $aliases) {
        Core::syslog(get_class($this) . ' -> Set array aliases [' . __LINE__ . ']');
        $this->_aliases = $aliases;
        return $this;
    }

    /**
     * Возвращает список алиасов
     *
     * @access public
     * @return array
     */
    public function getAliases() {
        return $this->_aliases;
    }

    /**
     * Добавление спсика алиасов к существующим
     *
     * @access public
     * @param array $aliases
     * @return $this
     */
    public function appendAliases(array $aliases) {
        Core::syslog(get_class($this) . ' -> Append array aliases [' . __LINE__ . ']');
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
    public function onInstalled($event) {
        if (!($handlerName = $this->i('autoloadHandler')))
            throw $this->exceptionService('Not specified "autoloadHandler"');
        spl_autoload_register([$this, $handlerName]);
        return true;
    }
}
