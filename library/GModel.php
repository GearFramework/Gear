<?php

namespace gear\library;

use \gear\Core;
use \gear\library\GObject;
use \gear\library\GException;
use gear\library\TEvents;
use gear\library\TBehaviors;
use gear\library\TPlugins;

/**
 * Базовый класс моделей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @php 5.4.x
 * @release 1.0.0
 */
class GModel extends GObject
{
    /* Traits */
    use TEvents;
    use TBehaviors;
    use TPlugins;
    /* Const */
    /* Private */
    /* Protected */
    protected $_pk = 'id';
    /* Public */

    /**
     * Конструктор класса
     * Принимает ассоциативный массив свойств объекта и их значений
     *
     * @access public
     * @param array $properties
     * @param null|object $owner
     * @return \gear\library\GModel
     */
    public function __construct(array $properties = [], $owner = null) { parent::__construct($properties, $owner); }

    /**
     * Клонирование
     *
     * @access public
     * @return void
     */
    public function __clone() { parent::__clone(); }

    /**
     * Установка названия поля являющимся PRIMARY KEY
     *
     * @access public
     * @param string $pk
     * @return $this
     */
    public function setPk($pk)
    {
        $this->_pk = $pk;
        return $this;
    }

    /**
     * Получение названия поля являющимся PRIMARY KEY
     *
     * @access public
     * @return string
     */
    public function getPk() { return $this->_pk; }

    /**
     * Возвращает объект-коллекцию в которой находится модель
     *
     * @access public
     * @return db\GDbCollection|null
     */
    public function getCollection()
    {
        return $this->owner instanceof \gear\library\GDbComponent ? $this->owner->getCollection() : null;
    }
}
