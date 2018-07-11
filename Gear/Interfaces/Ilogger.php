<?php

/**
 * Интерфейс логгера
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface ILogger
{
    /**
     * Метод сохранения log-записей
     *
     * @param $level
     * @param $message
     * @param array $context
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function log($level, $message, array $context = []);
}
