<?php

namespace gear\components\gear\process;
use \gear\Core;
use \gear\library\GModel;
use \gear\library\GException;

/** 
 * Класс процессов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GProcess extends GModel
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_instance = null;
    protected $_access = Core::ACCESS_PUBLIC;
    /* Public */
    public $rules = array();
    
    /**
     * Получение уровня доступа к процессу
     * 
     * @access public
     * @return Core::ACCESS_PRIVATE|Core::ACCESS_PROTECTED|Core::ACCESS_PUBLIC
     */
    public function getAccess() { return $this->_access; }
    
    /**
     * Запрет на установку уроня доступа извне. Уровень доступа возможно
     * пописать либо непосредстве при реализации нового класса процесса,
     * либо в описании компонента, управляющего процессами
     * 
     * @access public
     * @param integer one from Core::ACCESS_PRIVATE|Core::ACCESS_PROTECTED|Core::ACCESS_PUBLIC $value
     * @return void
     */
    public function setAccess($value) { $this->e('Свойство "access" только для чтения'); }
    
    /**
     * Возвращает массив правил обработки поступающих данных от пользоваля к
     * api-методам процесса
     * 
     * @access public
     * @return array
     */
    public function getRules() { return $this->rules; }
    
    /**
     * Обработчик по-умолчанию события возникающего перед исполнением
     * процесса
     * 
     * @access public
     * @param GEvent $event
     * @return bool
     */
    public function onBeforeExec($event) { return true; }
    
    /**
     * Обработчик по-умолчанию события возникающего после исполнения
     * процесса
     * 
     * @access public
     * @param GEvent $event
     * @param mixed $result
     * @return mixed
     */
    public function onAfterExec($event, $result = true) { return $result; }
}

/** 
 * Класс исключений процесса
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class ProcessException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
