<?php

namespace Gear\Interfaces\Storage;

/**
 * Интерфейс моделей связанных с хранением данных в хранилищах
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface StorageInterface
{
    /**
     * Возвращает подключение к хранилищу
     *
     * @return mixed
     */
    public function getHandler(): mixed;
}