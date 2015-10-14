<?php

namespace gear\library;

/**
 * Класс объектов-событий 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.4.x
 * @release 1.0.0
 */
class GEvent
{
    /* Const */
    /* Private */
    private $_sender = null;
    private $_args = [];
    private $_stopPropagation = false;
    /* Protected */
    /* Public */

    /**
     * Конструктор события
     * 
     * @access public
     * @param object $sender
     * @return GEvent
     */
    public function __construct($sender, array $args = [])
    {
        $this->_sender = $sender;
        $this->_args = $args;
    }
    
    /**
     * @access public
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter))
            return $this->$getter();
        if (isset($this->_args[$name]))
            return $this->_args[$name];
        return null;
    }
    
    /**
     * Установка дополнительных параметров события, которые могут быть
     * использованы обработчиками
     * 
     * @access public
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter))
            $this->$setter($value);
        else
            $this->_args[$name] = $value;
    }
    
    /**
     * Возвращает объект, который вызвал вызвал событие
     * 
     * @access public
     * @return object
     */
    public function getSender() { return $this->_sender; }
    
    /**
     * Запрет на последующую передачу события по цепочке обработчиков
     * 
     * @access public
     * @return $this
     */
    public function stopPropagation()
    {
        $this->_stopPropagation = true;
        return $this;
    }

    /**
     * Установка значение для stopPropagation
     *
     * @access public
     * @param bool $value
     * @return bool
     */
    public function setStopPropagation($value)
    {
        $this->_stopPropagation = (bool)$value;
        return $this;
    }

    /**
     * Возвращает значение stopPropagation
     *
     * @access public
     * @return bool
     */
    public function getStopPropagation() { return $this->_stopPropagation; }
}
