<?php

namespace Gear\Components\Db\Mysql;

use Gear\Library\Db\GDbCollection;

/**
 * Библиотека коллекции базы данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GMySqlCollection extends GDbCollection
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_defaultProperties = [
        'name' => '',
        'type' => 'InnoDB',
    ];
    protected $_factory = [
        'class' => '\gear\library\GModel',
    ];
    protected $_cursorFactory = [
        'class' => '\gear\components\db\mysql\GMySqlCursor',
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
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function truncate(): GDbCollection
    {
        $this->cursor->runQuery('TRUNCATE TABLE `%s`', $this->name);
    }
}
