<?php

namespace gear\components\gear\access;
use \gear\Core;
use \gear\library\GDbComponent;
use \gear\library\GDependencyIterator;
use \gear\library\GException;
use \gear\interfaces\IFactory;

/**
 * Компоенент для определения ресурсов приложения и разграничением доступа к 
 * ним
 *
 * @package Arquivo
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 10.12.2013
 */
class GAccess extends GDbComponent implements IFactory, \IteratorAggregate
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'itemClass' => '\\gear\\components\\gear\\access\\GAccessElement',
        'iterator' => '\\gear\\library\\GDependencyIterator',
    );
    protected static $_init = false;
    protected $_types = array
    (
        'model' => 'Модель',
        'object' => 'Объект модели',
        'property' => 'Свойство модели',
        'process' => 'Процесс',
        'api' => 'Api-метод',
    );
    /* Public */
    public $connectionName = 'mongo';
    public $dbName = null;
    public $collectionName = 'access';
    
    /**
     * Создание экземпляра класса элементов приложения
     * 
     * @access public
     * @param array $properties
     * @param null|string $class
     * @return object
     */
    public function factory(array $properties = array(), $class = null)
    {
        if (!$class)
            $class = $this->i('itemClass');
        $properties['owner'] = $this;
        return is_array($properties) ? new $class($properties) : null;
    }
    
    /**
     * Возвращает итератор всех элементов
     * 
     * @access public
     * @param array $crtiteria
     * @param array $sort
     * @param null|integer|string|array $limit
     * @return \gear\library\GDependencyIterator
     */
    public function getIterator($crtiteria = array(), $sort = array(), $limit = null)
    {
        $collection = new \gear\library\GProxyCollection(array('owner' => $this));
        return $collection->attach($this->_request($crtiteria, $sort, $limit));
    }
    
    /**
     * Формирование запроса к базе данных
     * 
     * @access protected
     * @param array $criteria
     * @param array $sort
     * @param null|integer|string|array $limit
     * @return GDbCursor
     */
    protected function _request($criteria = array(), $sort = array('caption' => 1), $limit = null)
    {
        return $this->getConnection()->find($criteria)->sort($sort)->limit($limit);
    }
    
    /**
     * Возвращает список всех элементов
     * 
     * @access public
     * @return \gear\library\GDependencyIterator
     */
    public function all() 
    {
        return $this->getIterator();
    }
    
    /**
     * Возвращает источник контента по его идентификатору, либо коллекцию 
     * источников из набора идентификаторов
     * 
     * @access public
     * @param integer|array $id
     * @return GSource|\gear\library\GDependencyIterator
     * @example GSourceComponent::byId(1)
     * @example GSourceComponent::byId(1, 3, 10)
     * @example GSourceComponent::byId(array(1, 3, 10)))
     */
    public function byId($id)
    {
        return is_array($id) || func_num_args() > 1 
               ? $this->getIterator(array('id' => array('$in' => is_array($id) ? $id : func_get_args())))
               : $this->factory($this->getConnection()->findOne(array('id' => (int)$id)));
    }
    
    public function byType($type)
    {
        return $this->getIterator(array('type' => $type));
    }
    
    /**
     * Возвращает набор источников, соответствующих указанному критерию
     * 
     * @access public
     * @param array $criteria
     * @param array $sort
     * @param null|integer|string|array $limit
     * @return \gear\library\GDependencyIterator
     */
    public function byCriteria($criteria = array(), $sort = array('caption' => 1), $limit = null)
    {
        return $this->getIterator($criteria, $sort, $limit);
    }
    
    public function insert($access)
    {
        $this->getConnection()->insert($access->props());
    }
    
    public function setTypes(array $types)
    {
        $this->_types = $types;
    }
    
    public function getTypes()
    {
        return $this->_types;
    }
}

/**
 * Исключения менеджера источников контента
 *
 * @package Arquivo
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 10.12.2013
 */
class AccessException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
