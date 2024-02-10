<?php

namespace Gear\Interfaces;

/**
 * Интерфейс событий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface EventInterface
{
    /**
     * Получение состояния всплытия события
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getBubble(): bool;

    /**
     * Возвращает поставщика события
     *
     * @return null|ObjectInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getSender(): ?ObjectInterface;

    /**
     * Установка или отмена всплытия события
     *
     * @param bool $value
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setBubble(bool $value);

    /**
     * Установка поставщика события
     *
     * @param string|ObjectInterface $sender
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setSender($sender);

    /**
     * Останавливает всплытие события
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function stopPropagation();
}


/**
 * Интерфейс классов-обработчиков событий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface EventHandlerInterface
{
    /**
     * Запуск обработчика события
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke();
}
