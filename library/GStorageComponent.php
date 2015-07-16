<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GComponent;
use \gear\library\GException;

/**
 * Класс компонентов, предоставляющих функции по хранению моделей (базы данных, файлы, xml и т.п.)
 *
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 16.07.2015
 * @php 5.3.x
 */
abstract class GStorageComponent extends GComponent
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
    public function storage() { return $this->getConnection(); }

    /**
     * Возвращает соединение с хранилищем объектов
     *
     * @access public
     * @return object
     */
    abstract public function getConnection();
}
