<?php

namespace gear\components\db\mysql;

use gear\library\db\GDbCollection;

class GMySqlCollection extends GDbCollection
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
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
        $this->cursor->runQuery('DROP TABLE `%s`', $this->name)->execute();
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        if (!$this->cursor) {
            $this->cursor = $this->factory($this->_cursorFactory);
            $this->cursor->runQuery('SELECT * FROM `%s`', $this->name);
        }
        return $this->cursor;
    }

    /**
     * Сброс результатов выполнения последнего запроса
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reset()
    {
        if ($this->_cursor) {
            $this->_cursor->free();
            unset($this->_cursor);
        }
    }

    /**
     * Очистка таблицы от записей
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function truncate()
    {
        $this->cursor->runQuery('TRUNCATE TABLE `%s`', $this->name)->execute();
    }
}
