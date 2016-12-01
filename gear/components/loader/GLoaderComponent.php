<?php

namespace gear\components\loader;

use gear\Core;
use gear\library\GComponent;

/**
 * Сервис-компонент автозагрузки классов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @property  array aliases
 * @since 0.0.1
 * @version 0.0.1
 */
class GLoaderComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = ['autoloadHandler' => 'loader'];
    protected static $_initialized = false;
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
     * @param string $className
     * @since 0.0.1
     * @version 0.0.1
     */
    public function loader(string $className)
    {
        if (isset($this->_aliases[$className]['class'])) {
            $alias = $className;
            $className = $this->_aliases[$className]['class'];
            class_alias($className, $alias);
        }
        if ($this->usePaths && isset($this->paths[$className]))
            $file = $this->paths[$className];
        else
            $file = ROOT . '/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file) && is_readable($file))
            include_once($file);
    }

    /**
     * Получение физического пути к уазанному элементу (файл, директория).
     *
     * @param string $namespace
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function resolvePath(string $namespace): string
    {
        if ($this->useResolvePaths && isset($this->resolvePaths[$namespace])) {
            $path = $this->resolvePaths[$namespace];
        } else {
            if (!$namespace) {
                $path = $namespace;
            } else if (!preg_match('/^([a-zA-Z]\:|\/)/', $namespace)) {
                $resolve = str_replace('\\', '/', $namespace);
                if ($resolve[0] == '/') {
                    $resolve = ROOT . $resolve;
                } else {
                    if (Core::isModuleInstalled('app')) {
                        $resolve = ROOT . '/' . str_replace('\\', '/', Core::app()->namespace) . '/' . $resolve;
                    } else {
                        $resolve = GEAR . '/' . $resolve;
                    }
                }
                $path = $resolve;
            } else {
                $path = $namespace;
            }
        }
        return $path;
    }

    /**
     * Установка алиаса для класса
     *
     * @param string $className
     * @param string $alias
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAlias(string $className, string $alias)
    {
        $this->_aliases[$alias] = ['class' => $className];
    }

    /**
     * Возвращает оригинальное название класса, которому соответствует указанный алиас
     *
     * @param string $alias
     * @return null|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAlias(string $alias)
    {
        return isset($this->_aliases[$alias]['class']) ? $this->_aliases[$alias]['class'] : null;
    }

    /**
     * Устанавливает новый список алиасов
     *
     * @param array $aliases
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAliases(array $aliases)
    {
        $this->_aliases = $aliases;
    }

    /**
     * Возвращает список алиасов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAliases(): array
    {
        return $this->_aliases;
    }

    /**
     * Добавление спсика алиасов к существующим
     *
     * @access public
     * @param array $aliases
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function appendAliases(array $aliases)
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    /**
     * Обработчик события onInstalled по-умолчанию
     * Регистрация метода автозагрузки классов
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterInstallService()
    {
        if (!($handlerName = static::i('autoloadHandler'))) {
            Core::syslog(Core::CRITICAL, 'Not specified <{handler}> property', ['handler' => $handlerName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
            throw self::exceptionService('Not specified <{handler}> property', ['handler' => 'autoloadHandler']);
        }
        spl_autoload_register([$this, $handlerName]);
        Core::syslog(Core::INFO, 'Loader component registered autoload handler <{handler}>', ['handler' => $handlerName, '__func__' => __METHOD__, '__line__' => __LINE__], true);
        return parent::afterInstallService();
    }
}