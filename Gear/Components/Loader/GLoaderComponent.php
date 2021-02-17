<?php

namespace Gear\Components\Loader;

use Gear\Core;
use Gear\Interfaces\AutoloaderInterface;
use Gear\Library\GComponent;

/**
 * Сервис-компонент автозагрузки классов
 *
 * @package Gear Framework
 *
 * @property array aliases
 * @property array paths
 * @property array resolvePaths
 * @property bool usePaths
 * @property bool useResolvePaths
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GLoaderComponent extends GComponent implements AutoloaderInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static array $_config = [
        'autoloadHandler' => 'loader'
    ];
    /** @var array $_aliases список классов и соответствующих им алиасов (ключ массива - алиас, значение - класс) */
    protected $_aliases = [];
    /** @var array $_paths список директорий и соответсвующих им классов */
    protected $_paths = [];
    /** @var array $_resolvePaths список преобразованных директорий и соответствующих им оригинальных путей */
    protected $_resolvePaths = [];
    protected $_usePaths = false;
    protected $_useResolvePaths = false;
    /* Public */

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
     * Возвращает оригинальное название класса, которому соответствует указанный алиас
     *
     * @param string $alias
     * @return null|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAlias(string $alias)
    {
        return isset($this->_aliases["\\$alias"]['class']) ? $this->_aliases["\\$alias"]['class'] : null;
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
     * Возвращает массив с указанием директорий с соответсвующих им классом
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPaths(): array
    {
        return $this->_paths;
    }

    /**
     * Возвращает массив соответствия пути и значением того, как должен "резолвится"
     * (преобразовываться) этот путь
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getResolvePaths(): array
    {
        return $this->_resolvePaths;
    }

    /**
     * Возвращает true, если будет использоваться массив путей к классам
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUsePaths(): bool
    {
        return $this->_usePaths;
    }

    /**
     * Возвращает true, если будет использоваться массив преобразования путей и пространств имен
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUseResolvePaths(): bool
    {
        return $this->_useResolvePaths;
    }

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
        $alias = $className;
        if (($className = $this->getAlias($alias))) {
            class_alias($className, $alias);
        } else {
            $className = $alias;
        }
        if ($this->usePaths && isset($this->paths[$className])) {
            $file = $this->paths[$className];
        } else {
            $file = ROOT . '/' . str_replace('\\', '/', $className) . '.php';
        }
        if (file_exists($file) && is_readable($file)) {
            include_once($file);
        }
    }

    /**
     * Обработчик события onInstalled по-умолчанию
     * Регистрация метода автозагрузки классов
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function onAfterInstallService($event)
    {
        if (!($handlerName = static::i('autoloadHandler'))) {
            throw self::ServiceException('Not specified <{handler}> property', ['handler' => 'autoloadHandler']);
        }
        spl_autoload_register([$this, $handlerName]);
        return true;
    }

    /**
     * Получение физического пути к уазанному элементу (файл, директория).
     *
     * @param string $namespace
     * @return string
     * @throws \CoreException
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
            } elseif (!preg_match('/^([a-zA-Z]\:|\/)/', $namespace)) {
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
        if (!isset($this->_aliases[$alias])) {
            $this->_aliases[$alias] = ['class' => $className];
        }
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
     * Усновка новых путей расположения файлов с классами
     *
     * @param array $paths
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setPaths(array $paths)
    {
        $this->_paths = $paths;
    }

    /**
     * Усновка новых путей преобразования
     *
     * @param array $paths
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setResolvePaths(array $paths)
    {
        $this->_resolvePaths = $paths;
    }

    /**
     * Установка значения будут ли использоваться пути расположения классов или нет
     *
     * @param bool $usePaths
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setUsePaths(bool $usePaths)
    {
        $this->_usePaths = $usePaths;
    }

    /**
     * Установка значения будут ли использоваться пути преобразования или нет
     *
     * @param bool $useResolvePaths
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setUseResolvePaths(bool $useResolvePaths)
    {
        $this->_useResolvePaths = $useResolvePaths;
    }
}
