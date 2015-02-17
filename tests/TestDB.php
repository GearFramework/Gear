<?php

namespace gear\tests;

class TestDb
{
    protected $_connection = null;
    protected $_dbName = null;
    protected $_collectionName = null;

    public function __construct(array $connection)
    {
        $class = $connection['class'];
        unset($connection['class']);
        $this->_dbName = $connection['dbName'];
        unset($connection['dbName']);
        $this->_collectionName = $connection['collectionName'];
        unset($connection['collectionName']);
        $this->_connection = $class::it($connection);
    }

    public function find($criteria)
    {
        return $this->_connection->selectCollection($this->_dbName, $this->_collectionName)->find($criteria);
    }

    public function query($cursor)
    {
        return $cursor->__toString();
    }
}