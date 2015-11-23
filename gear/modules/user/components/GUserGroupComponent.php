<?php

namespace gear\modules\user\components;
use gear\Core;
use gear\library\GDBComponent;
use gear\interfaces\IFactory;

class GUserGroupComponent extends GDbComponent implements IFactory, \IteratorAggregate
{
    /* Const */
    /* Private */
    private $_factoryClass = '\gear\modules\user\models\GUserGroup';
    private $_collection = '\gear\library\GProxyCollection';
    /* Protected */
    /* Public */
    
    public function factory(array $properties = array(), $class = null)
    {
        if (!$class) $class = $this->factoryClass;
        $properties['owner'] = $this;
        return new $class($properties);
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
        $classIterator = $this->collection;
        $collection = new $classIterator(array('owner' => $this));
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
        $reference = $this->reference;
        list($groupId, $userId) = each($reference['key']);
        $criteria[$this->collectionName . '.id'] = ':' . $reference['collectionName'] . '.' . $groupId;
        return $this->getConnection()->find($criteria)
                                     ->inner($reference['collectionName'], array($reference['collectionName'] . '.' . $userId => $this->owner->id))
                                     ->sort($sort)
                                     ->limit($limit);
    }
    
    public function setFactoryClass($class)
    {
        $this->_factoryClass = $class;
    }
    
    public function getFactoryClass()
    {
        return $this->_factoryClass;
    }
    
    public function setCollection($collectionClass)
    {
        $this->_collection = $collectionClass;
    }
    
    public function getCollection()
    {
        return $this->_collection;
    }
} 