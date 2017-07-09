<?php

namespace gear\library\db;

use gear\library\GModel;
use gear\library\GStaticFactory;
use gear\traits\TDelegateFactory;
use gear\traits\TFactory;

/**
 * Библиотека коллекций (таблиц)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbCollection extends GModel implements \IteratorAggregate
{
    /* Traits */
    use TFactory;
    use TDelegateFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected $_alias = "";
    protected $_current = null;
    protected $_cursorFactory = [
        'class' => '\gear\library\db\GDbCursor',
    ];
    protected $_items = [];
    /* Public */

    public function __call(string $name, array $arguments)
    {
        $result = null;
        if (method_exists($this->_cursorFactory['class'], $name)) {
            $result = $this->cursor->$name(... $arguments);
        } else {
            $result = parent::__call($name, $arguments);
        }
        return $result;
    }

    /**
     * Удаление базы данных
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function drop();

    /**
     * Возвращает псевдоним для коллекции
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
     * Возвращает соединение с сервером базы данных
     *
     * @return GDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): GDbConnection
    {
        return $this->owner->getConnection();
    }

    /**
     * Возвращает текущий выполняемый запрос
     *
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrent(): GDbCursor
    {
        return $this->_current;
    }

    /**
     * Возвращает инстанс курсора для работы с запросами
     *
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): GDbCursor
    {
        $this->current = GStaticFactory::factory($this->_cursorFactory, $this);
        return $this->current;
    }

    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDatabase(): GDbDatabase
    {
        return $this->owner;
    }

    /**
     * Возвращает ресурс соединения с сервером базы данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHandler()
    {
        return $this->owner->handler;
    }

    /**
     * Возвращает ID последней вставленной записи в таблицу
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function lastInsertId(): int
    {
        return $this->current ? $this->current->lastInsertId : 0;
    }

    /**
     * Удаление таблицы
     *
     * @param null|object $model
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($model = null)
    {
        if ($model) {
            $this->cursor->remove($model);
        } else {
            $this->drop();
        }
    }

    /**
     * Сброс результатов выполнения последнего запроса
     *
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reset(): GDbCollection
    {
        if ($this->current) {
            $this->current->reset();
        }
        return $this;
    }

    /**
     * Установка псевдонима для коллекции
     *
     * @param string $alias
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAlias(string $alias)
    {
        $this->_alias = $alias;
    }

    /**
     * Установка текущего выполняемого запроса
     *
     * @param GDbCursor $cursor
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCurrent(GDbCursor $cursor)
    {
        $this->_current = $cursor;
    }

    /**
     * Очистка таблицы от записей
     *
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function truncate(): GDbCollection;
}
