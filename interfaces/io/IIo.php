<?php

namespace gear\interfaces;

/**
 * Интерфейс ввода-вывода
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 17.08.2015
 * @release 1.0.0
 */
interface IIo
{
    /**
     * Открытие ввода/вывода
     *
     * @access public
     * @return void
     */
    public function open();

    /**
     * Чтение
     *
     * @access public
     * @return void
     */
    public function read();

    /**
     * Запись
     *
     * @access public
     * @return void
     */
    public function write();

    /**
     * Закрытие ввода/вывода
     *
     * @access public
     * @return void
     */
    public function close();
}
