<?php

namespace Gear\Interfaces;

/**
 * Интерфейс логгера
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface LoggerInterface
{
    /**
     * Метод сохранения log-записей
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function log($level, string $message, array $context = []);
}
