<?php

namespace Gear\Interfaces;

use Gear\Interfaces\Services\ComponentInterface;

/**
 * Интерфейс автолоадера классов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface AutoloaderInterface extends ComponentInterface
{
    /**
     * Метод автоматической загрузки классов
     * - Поддержка алиасов
     * - Поддержка пользовательских путей расположения файлов с классами
     *
     * @param   string $className
     * @return  void
     */
    public function loadClass(string $className): void;

    /**
     * Получение физического пути к указанному элементу (файл, директория).
     *
     * @param   string $namespace
     * @return  string
     */
    public function resolvePath(string $namespace): string;
}
