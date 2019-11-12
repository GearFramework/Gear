<?php

namespace Gear\Components\Db\MySql;

use Gear\Interfaces\DbCollectionInterface;
use Gear\Library\Db\GDbCollection;

/**
 * Библиотека коллекции базы данных
 *
 * @package Gear Framework
 *
 * @property string alias
 * @property GMySqlConnectionComponent connection
 * @property GMySqlDatabase database
 * @property \mysqli handler
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GMySqlCollection extends GDbCollection implements DbCollectionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_cursorFactory = [
        'class' => '\Gear\Components\Db\MySql\GMySqlCursor',
    ];
    protected static $_defaultProperties = [
        'name' => '',
        'type' => 'InnoDB',
    ];
    protected $_factoryProperties = [
        'class' => '\Gear\Components\Db\MySql\GMySqlCursor',
    ];
    protected $_model = [
        'class' => '\Gear\Components\Db\MySql\GMySqlCursor',
    ];
    protected $_alias = '';
    /* Public */

    /**
     * Удаление таблицы
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function drop()
    {
        $this->cursor->runQuery('DROP TABLE `%s`', $this->name);
    }

    /**
     * Возвращает название псевдонима таблицы
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAlias(): string
    {
        return $this->_alias;
    }

    /**
     * Возвращает итератор со всеми найденными записями в таблице
     *
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator(): \Iterator
    {
        return $this->delegate($this->cursor->find());
    }

    /**
     * Установка псевдонима для таблицы
     *
     * @param string $alias
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAlias(string $alias)
    {
        $this->_alias = $alias;
    }

    /**
     * Очистка таблицы от записей
     *
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function truncate(): DbCollectionInterface
    {
        $this->cursor->runQuery('TRUNCATE TABLE `%s`', $this->name);
        return $this;
    }
}
