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
    public function log(string $level, string $message, array $context = []);
}

/**
 * Интерфейс компонента логгирования операций
 *
 * @package Gear Framework
 *
 * @property array loggers
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface LoggerComponentInterface
{
    /**
     * @param string $name
     * @param PluginInterface|array $logger
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function addLogger(string $name, $logger);

    /**
     * Запись в лог сообщения типа ALERT
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function alert($message, array $context = []);

    /**
     * Запись в лог сообщения типа CRITICAL
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function critical($message, array $context = []);

    /**
     * Запись в лог сообщения типа DEBUG
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function debug($message, array $context = []);

    /**
     * Запись в лог сообщения типа EMERGENCY
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function emergency($message, array $context = []);

    /**
     * Запись в лог сообщения типа ERROR
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function error($message, array $context = []);

    /**
     * Запись в лог сообщения типа EXCEPTION
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function exception($message, array $context = []);

    /**
     * Возвращает массив именн используемых логгеров
     * Имя логгера - это название плагина данного компонента
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getLoggers(): array;

    /**
     * Запись в лог сообщения типа INFO
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function info($message, array $context = []);
    /**
     * Запись в лог
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function log(string $level, string $message, array $context = []);
    /**
     * Запись в лог сообщения типа NOTICE
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function notice($message, array $context = []);
    /**
     * Удаление логгера
     *
     * @param $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public function removeLogger($name);
    /**
     * Запись в лог сообщения типа WARNING
     *
     * @param $message
     * @param array $context
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function warning($message, array $context = []);
}