<?php

namespace gear\library;

/**
 * Коллекция объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GCollection extends GModel implements \Iterator
{
    /* Traits */
    /* Const */
    const SORT_ASC = 1;
    const SORT_DESC = -1;
    /* Private */
    /* Protected */
    protected $_items = [];
    protected $_key = 'id';
    /* Public */
    
    public function __construct($properties = [], $owner = null)
    {
        if (isset($properties['items'])) {
            foreach($properties['items'] as $item) {
                $this->add($item);
            }
            unset($properties['items']);
        }
        parent::__construct($properties, $owner);
    }

    public function add($item)
    {
        $key = $item->{$this->key};
        $key ? $this->_items[$key] = $item : $this->_items[] = $item;
    }

    public function getItems(): array
    {
        return $this->_items;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function remove($item)
    {
        $result = false;
        if (is_object($item)) {
            $key = $item->{$this->key};
            if ($key && isset($this->_items[$key])) {
                $result = $this->_items[$key];
                unset($this->_items[$key]);
            } else {
                foreach($this->_items as $key => $i) {
                    if ($i === $item) {
                        $result = $this->_items[$key];
                        unset($this->_items[$key]);
                        break;
                    }
                }
            }
        } else if (isset($this->_items[$item])) {
            $result = $this->_items[$item];
            unset($this->_items[$item]);
        }
        return $result;
    }

    public function setItems(array $items)
    {
        $this->_items = $items;
    }

    public function setKey($key)
    {
        $this->_key = $key;
    }

    public function sort($field, int $order = self::SORT_ASC, int $flags = 0)
    {
        if ($field === $this->key) {
            $order === SORT_ASC ? asort($this->_items, $flags) : arsort($this->_items, $flags);
        } else {
            uasort($this->_items, function($item1, $item2) use ($field, $order) {
                $val1 = $item1->$field;
                $val2 = $item2->$field;
                if (is_numeric($val1) && !is_string($val2)) {
                    $result = $val1 === $val2 ? 0 : ($val1 < $val2 ? -1: 1);
                } else {
                    $result = strcasecmp($val1, $val2);
                }
                return $result == 0 ? 0 : ($result == -1 ? ($order == self::SORT_ASC ? -1 : 1) : ($order == self::SORT_ASC ? 1 : -1));
            });
        }
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->_items);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->_items);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->_items);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return current($this->_items) !== false ? true : false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->_items);
    }
}