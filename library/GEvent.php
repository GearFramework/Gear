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
 * @php 5.3.x
 */
class GEvent
{
    /* Const */
    /* Private */
    private $_sender = null;
    private $_args = array();
    /* Protected */
    /* Public */
    public $stopPropagation = false;
    
    /**
     * Конструктор события
     * 
     * @access public
     * @param object $sender
     * @return GEvent
     */
    public function __construct($sender, array $args = array())
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
    public function __set($name, $value) { $this->_args[$name] = $value; }
    
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
        $this->stopPropagation = true;
        return $this;
    }
}
