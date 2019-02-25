<?php

namespace Gear\Interfaces;

/**
 * Интерфейс компонентов локализации
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
interface InternationalInterface
{
    /**
     * Добавление новой секции сообщений
     *
     * @param string $section
     * @param array $messages
     * @since 0.0.1
     * @version 0.0.1
     */
    public function attachSection(string $section, array $messages);

    /**
     * Возвращает массив сообщений, если указано название секции, то возвращает массив сообщений из указанной
     * секции сообщений
     *
     * @param string $section
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMessages(string $section = ''): array;

    /**
     * Возвращает перевод указанного сообщения
     *
     * @param string $message
     * @param string $section
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function tr(string $message, string $section = ''): string;
}
