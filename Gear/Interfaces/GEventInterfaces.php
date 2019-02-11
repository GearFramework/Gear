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
interface GEventInterface
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
     * @return null|GObjectInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getSender(): ?GObjectInterface;

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
     * @param string|GObjectInterface $sender
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
interface GEventHandlerInterface
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
