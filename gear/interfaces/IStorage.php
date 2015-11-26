<?php

namespace gear\interfaces;

/**
 * Интерфейс компонентов, предоставляющих функции по хранению моделей (базы данных, файлы, xml и т.п.)
 *
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 16.07.2015
 * @php 5.4.x or higher
 * @release 1.0.0
 */
interface IStorage
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает хранилище объектов
     *
     * @access public
     * @return object
     */
    public function storage();
}
