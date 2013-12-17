<?php

namespace gear\library;

use \gear\Core;
use \gear\library\GException;

class GBaseObject
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = array();
    protected $_owner = null;
    /* Public */

    /**
     * Конструктор класса
     * Принимает ассоциативный массив свойств объекта и их значений
     * 
     * @access protected
     * @param array $properties
     * @return void
     */
    protected function __construct(array $properties = array())
    {
        foreach($properties as $name => $value)
            $this->$name = $value;
    }
    
    /**
     * Установка значения для свойства объекта, для которого не реализован сеттер
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
            $this->_properties[$name] = $value;
    }
    
    /**
     * Если для свойства реализован геттер, то запускает его.
     * Если $name является свойством объекта, то возвращает его значение
     * Во всех остальных случаях возвращается null
     * 
     * @access public
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter))
            return $this->$getter();
        else
            return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
    }

    public function __call($name, $args)
    {
        if (is_object($this->_owner))
        {
            array_unshift($args, $this);
            return call_user_func_array(array($this->_owner, $name), $args);
        }
        $this->e('Метод ":methodName" не реализован', array('methodName' => $name));
    }
    
    /**
     * Возвращает true если $name является:
     * - событием, для которого имеются обработчики
     * - поведением
     * - свойством объекта
     * иначе возвращает false
     * 
     * @access public
     * @param string $name
     * @return boolen
     */
    public function __isset($name)
    {
        return isset($this->_properties[$name]);
    }
    
    /**
     * Метод производит удаление:
     * - обработчиков события, если $name таковым является
     * - отключает поведение, если $name является названием поведения
     * - удаляет свойство объекта, если таковое имеется
     * 
     * @access public
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->_properties[$name]))
            unset($this->_properties[$name]);
    }
    
    /**
     * Возвращает имя класса объекта
     * 
     * @access public
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * Установка владельца объекта
     * 
     * @access public
     * @param object $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        if (!is_object($owner))
            $this->e('Владелец должен быть объектом');
        $this->_owner = $owner;
        return $this;
    }
    
    /**
     * Получение владельца объекта
     * 
     * @acess public
     * @return object
     */
    public function getOwner()
    {
        return $this->_owner;
    }
}