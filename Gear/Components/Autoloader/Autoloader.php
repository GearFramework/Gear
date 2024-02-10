<?php

namespace Gear\Components\Autoloader;

use Gear\Core;
use Gear\Interfaces\AutoloaderInterface;
use Gear\Library\Services\Component;

/**
 * Автолоадер фреймворка
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Autoloader extends Component implements AutoloaderInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /** @var string[] $aliases */
    private array $aliases = [];
    /** @var string[] $resolvePaths */
    private array $resolvePaths = [];
    /** @var string[] $classPaths */
    private array $classPaths = [];
    /* Protected */
    /* Public */

    /**
     * Метод автоматической загрузки классов
     *
     * @param   string $className
     * @return  void
     */
    public function loadClass(string $className): void
    {
        $alias = $className;
        $className = $this->getAlias($alias)
            ? class_alias($className, $alias)
            : $alias;
        $filename = $this->classPaths[$className] ?? $this->resolvePath($className);
        if (file_exists($filename) && is_readable($filename)) {
            include_once $filename;
        }
    }

    /**
     * Возвращает оригинальное название класса, которому соответствует указанный алиас
     *
     * @param   string $alias
     * @return  null|string
     */
    public function getAlias(string $alias): ?string
    {
        return isset($this->aliases[$alias]) ?? null;
    }

    /**
     * Установка алиаса для класса
     *
     * @param   string $className
     * @param   string $alias
     * @return  void
     */
    public function setAlias(string $className, string $alias): void
    {
        if (isset($this->aliases[$alias]) === false) {
            $this->aliases[$alias] = $className;
        }
    }

    /**
     * Получение физического пути к указанному элементу (файл, директория).
     *
     * @param   string $namespace
     * @return  string
     */
    public function resolvePath(string $namespace): string
    {
        if (isset($this->resolvePaths[$namespace])) {
            return $this->resolvePaths[$namespace];
        }
        if (empty($namespace)) {
            return $namespace;
        }
        if (preg_match('/^([a-zA-Z]:|\/)/', $namespace)) {
            return $namespace;
        }
        $resolve = str_replace('\\', '/', $namespace);
        if ($resolve[0] == '/') {
            return PRIVATE_ROOT . $resolve;
        }
        if (Core::isInstalled('app')) {
            $resolvedNamespace = str_replace('\\', '/', Core::app()->namespace);
            return PRIVATE_ROOT . "/{$resolvedNamespace}/{$resolve}";
        }
        return GEAR_ROOT . '/' . $resolve;
    }

    /**
     * Выполняется после установки сервиса
     *
     * @return void
     */
    public function afterInstall(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }
}
