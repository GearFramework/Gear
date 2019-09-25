<?php

namespace Gear\Library\Db;

use Gear\Interfaces\DbCollectionInterface;
use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbDatabaseInterface;
use Gear\Library\GModel;
use Gear\Library\GEvent;
use Gear\Traits\Db\Mysql\DbCursorFactoryTrait;
use Gear\Traits\Factory\DelegateFactoryTrait;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Библиотека базы данных
 *
 * @package Gear Framework
 *
 * @property DbConnectionInterface connection
 * @property null|DbCollectionInterface current
 * @property array cursorFactory
 * @property DbDatabaseInterface database
 * @property array factoryProperties
 * @property mixed handler
 * @property string name
 * @property DbConnectionInterface owner
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GDbDatabase extends GModel implements \IteratorAggregate, DbDatabaseInterface
{
    /* Traits */
    use DbCursorFactoryTrait;
    use DelegateFactoryTrait;
    use FactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_current = null;
    protected $_cursorFactory = [
        'class' => '\Gear\Library\Db\GDbCursor',
    ];
    protected $_factoryProperties = [
        'class' => '\Gear\Library\Db\GDbCollection',
    ];
    protected $_handler = null;
    protected $_items = [];
    protected $_model = [
        'class' => '\Gear\Library\Db\GDbCollection',
    ];
    /* Public */

    /**
     * Генерация события после выбоа базы данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterSelect()
    {
        return $this->trigger('onAfterConnect', new GEvent($this, ['target' => $this]));
    }

    /**
     * Генерация события до выбора базы данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeSelect()
    {
        return $this->trigger('onBeforeConnect', new GEvent($this, ['target' => $this]));
    }

    /**
     * Создание базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function create(): DbDatabaseInterface;

    /**
     * Удаление базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function drop(): DbDatabaseInterface;

    /**
     * Возвращает подключение к серверу базы данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface
    {
        return $this->owner;
    }

    /**
     * Возвращает текущую выбранную коллекцию
     *
     * @return null|DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCurrent(): ?DbCollectionInterface
    {
        return $this->_current;
    }

    /**
     * Возвращает базу данных, т.е. саму себя
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getDatabase(): DbDatabaseInterface
    {
        return $this;
    }

    /**
     * Возвращает ресурс подключения к базе данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHandler()
    {
        return $this->getConnection()->handler;
    }

    /**
     * Удаление базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove(): DbDatabaseInterface
    {
        $this->drop();
    }

    /**
     * Выбор текущей базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function select(): DbDatabaseInterface;

    /**
     * Возвращает коллекцию
     *
     * @param string $name
     * @param string $alias
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectCollection(string $name, string $alias = ''): DbCollectionInterface
    {
        if (isset($this->_items[$name])) {
            if ($alias) {
                $this->_items[$name]->alias = $alias;
            }
            if (!$this->current || $this->current->name !== $name) {
                $this->current = $this->_items[$name];
            }
        } else {
            $properties = ['name' => $name, 'alias' => $alias];
            $this->current = $this->_items[$name] = $this->factory($properties, $this);
        }
        return $this->current;
    }

    /**
     * Установка текущей коллекции
     *
     * @param DbCollectionInterface $current
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setCurrent(DbCollectionInterface $current)
    {
        $this->_current = $current;
    }
}
