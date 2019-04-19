<?php

namespace Gear\Traits\Db\Mysql;

use Gear\Core;
use Gear\Interfaces\DbCollectionInterface;
use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Interfaces\DbDatabaseInterface;
use Gear\Interfaces\ModelInterface;
use Gear\Interfaces\ServiceInterface;
use Gear\Traits\ServiceContainedTrait;

/**
 * Трейт компонентов для выполнения операций с моделями
 * в базах данных
 *
 * @package Gear Framework
 *
 * @property string alias
 * @property string collectionName
 * @property DbConnectionInterface connection
 * @property string connectionName
 * @property DbCursorInterface cursor
 * @property string dbName
 * @property DbCursorInterface defaultCursor
 * @property string primaryKeyName
 * @proeprty mixed primaryKey
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait DbStorageTrait
{
    protected $_servicesHandled = [];

    /**
     * Добавление модели в набор (сохранение в коллекции-таблице в базе данных)
     *
     * @param array|ModelInterface|array of IModel $model
     * @return int
     * @uses \Gear\Helpers\ArrayHelper as \Arrays
     * @since 0.0.1
     * @version 0.0.2
     */
    public function add($model): int
    {
        if ($model instanceof ModelInterface) {
            $model = $model->props();
        } elseif (is_array($model)) {
            if (!\Arrays::isAssoc($model)) {
                $models = [];
                foreach ($model as $props) {
                    if ($props instanceof ModelInterface) {
                        $models[] = $props->props();
                    } else {
                        $models[] = $props;
                    }
                }
                $model = $models;
            }
        }
        return $this->selectCollection()->insert($model);
    }

    /**
     * Выборка всех моделей из коллекции
     *
     * @param array|DbCursorInterface $sort
     * @return iterable
     * @since 0.0.1
     * @version 0.0.2
     */
    public function all($sort = []): iterable
    {
        return $this->getIterator($this->getDefaultCursor()->sort($sort));
    }

    /**
     * Выборка модели по значению первичного ключа
     *
     * @param int|string $pkValue
     * @return \Gear\Interfaces\ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function byPk($pkValue)
    {
        return $this->findOne([$this->primaryKeyName => "'$pkValue'"]);
    }

    /**
     * Возвращает количество элементов в коллекции, удовлетворяющих
     * критерию
     *
     * @param array|DbCursorInterface $criteria
     * @return int
     * @since 0.0.1
     * @version 0.0.2
     */
    public function count($criteria = []): int
    {
        $cursor = $this->getDefaultCursor();
        return $cursor->where($criteria)->count();
    }

    /**
     * Возвращает true, если указанный в критерии элемент существует в коллекции
     *
     * @param array|DbCursorInterface $criteria
     * @return bool
     * @since 0.0.1
     * @version 0.0.2
     */
    public function exists($criteria = []): bool
    {
        return $this->getDefaultCursor()->exists($criteria);
    }
    /**
     * Поиск моделей по указанному критерию
     *
     * @param array|string|DbCursorInterface $criteria
     * @param array|string $fields
     * @return iterable
     * @since 0.0.1
     * @version 0.0.2
     */
    public function find($criteria = [], $fields = []): iterable
    {
        return $this->getIterator($this->getDefaultCursor()->find($criteria, $fields));
    }

    /**
     * Поиск модели, соответствующей указанному критерию
     *
     * @param array|string|DbCursorInterface $criteria
     * @param array $fields
     * @param array $sort
     * @return \Gear\Interfaces\ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function findOne($criteria = [], $fields = [], $sort = [])
    {
//        if ($criteria instanceof IDbCursor) {
//            $criteria->findOne($criteria);
//        } else {
        $result = $this->selectCollection($this->alias)->findOne($criteria, $fields, $sort);
//        }
        return $result ? $this->factory($result) : $result;
    }

    /**
     * Возвращает алиас для коллекции
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
     * Возвращает название таблицы
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollectionName(): string
    {
        return $this->_collectionName;
    }

    /**
     * Возвращает компонент подключения к базе данных
     *
     * @return DbConnectionInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface
    {
        if (!$this->_connection) {
            $this->_connection = Core::c($this->connectionName);
        }
        return $this->_connection;
    }

    /**
     * Возвращает название компонента подключения к серверу базы данных
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnectionName(): string
    {
        return $this->_connectionName;
    }

    /**
     * Возвращает курсор коллекции
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCursor(): DbCursorInterface
    {
        return $this->selectCollection($this->alias)->cursor;
    }

    /**
     * Возвращает название базы данных
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDbName(): string
    {
        return $this->_dbName;
    }

    /**
     * Возвращает курсор с параметрами по-умолчанию
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getDefaultCursor(): DbCursorInterface
    {
        /**
         * @var DbCursorInterface $cursor
         */
        $cursor = $this->selectCollection($this->alias ? $this->alias : '')->find();
        if ($this->_defaultParams['where']) {
            $this->_prepareDefaultWhere($cursor, $this->_defaultParams['where']);
        }
        if ($this->_defaultParams['fields']) {
            $this->_prepareDefaultFields($cursor, $this->_defaultParams['fields']);
        }
        if ($this->_defaultParams['joins']) {
            $this->_prepareDefaultJoins($cursor, $this->_defaultParams['joins']);
        }
        if ($this->_defaultParams['left']) {
            $this->_prepareDefaultLeft($cursor, $this->_defaultParams['left']);
        }
        if ($this->_defaultParams['right']) {
            $this->_prepareDefaultRight($cursor, $this->_defaultParams['right']);
        }
        if ($this->_defaultParams['sort']) {
            $cursor->sort($this->_defaultParams['sort']);
        }
        if ($this->_defaultParams['limit']) {
            $cursor->limit($this->_defaultParams['limit']);
        }
        return $cursor;
    }

    /**
     * Возвращает итератор с записями
     *
     * @param iterable|string $cursor
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator($cursor = null): iterable
    {
        if (is_iterable($cursor)) {
            $cursor = $this->delegate($cursor);
        } elseif (is_string($cursor)) {
            $cursor = $this->delegate($this->cursor->runQuery($cursor));
        } else {
            $cursor = $this->delegate($this->getDefaultCursor());
        }
        return $cursor;
    }

    /**
     * Возвращает значение PRIMARYKEY поля
     *
     * @param array|ModelInterface $object
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrimaryKey($object)
    {
        if ($object instanceof ModelInterface) {
            return $object->props($this->primaryKeyName);
        } elseif (is_array($object) && isset($object[$this->primaryKeyName])) {
            return $object[$this->primaryKeyName];
        }
        return '';
    }

    /**
     * Возвращает название поля у которого установлен PRIMARYKEY
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrimaryKeyName(): string
    {
        return $this->alias ? $this->alias . '.' . $this->_primaryKeyName : $this->_primaryKeyName;
    }

    /**
     * Удаление модели
     *
     * @param array|ModelInterface $model
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove($model)
    {
        $this->selectCollection()->remove($model);
    }

    /**
     * Сохранение модели
     *
     * @param array|ModelInterface $model
     * @return int
     * @since 0.0.1
     * @version 0.0.2
     */
    public function save($model): int
    {
        if ($model instanceof ModelInterface) {
            $model = $model->props();
        } elseif (is_array($model)) {
            $models = [];
            foreach ($model as $props) {
                if ($props instanceof ModelInterface) {
                    $models[] = $props->props();
                } else {
                    $models[] = $props;
                }
            }
            $model = $models;
        }
        $this->selectCollection($this->alias)->save($model);
    }

    /**
     * Выбор коллекции
     *
     * @param string $alias
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectCollection(?string $alias = ""): DbCollectionInterface
    {
        return $this->connection->selectCollection($this->dbName, $this->collectionName, $alias ? $alias : $this->alias);
    }

    /**
     * Выбор базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectDB(): DbDatabaseInterface
    {
        return $this->connection->selectDB($this->dbName);
    }

    /**
     * Установка алиаса для коллекции
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
     * Устновка названия коллекции, в которой располагаются модели
     *
     * @param string $collectionName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCollectionName(string $collectionName)
    {
        $this->_collectionName = $collectionName;
    }

    /**
     * Устновка подключения к серверу базы данных
     *
     * @param DbConnectionInterface $connection
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setConnection(DbConnectionInterface $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Установка названия компонента, выполняющего подключение к
     * серверу базы данных
     *
     * @param string $connectionName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setConnectionName(string $connectionName)
    {
        $this->_connectionName = $connectionName;
    }

    /**
     * Установка названия базы данных с коллекциями моделей
     *
     * @param string $dbName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDbName(string $dbName)
    {
        $this->_dbName = $dbName;
    }

    /**
     * Обновление существующей модели
     *
     * @param array|ModelInterface|array of IModel $model
     * @param array|DbCursorInterface $criteria
     * @return int
     * @since 0.0.1
     * @version 0.0.2
     */
    public function update($model, $criteria = []): int
    {
        $result = 0;
        if ($model instanceof ModelInterface) {
            if ($model->onBeforeUpdate()) {
                $result = $this->selectCollection()->update($model);
                $model->onAfterUpdate();
            }
        } else {
            $result = $this->selectCollection()->update($model, $criteria);
        }
        return $result;
    }

    /**
     * @param string $path
     * @return ServiceInterface
     * @throws \CoreException
     */
    protected function _getComponentFromPath(string $path): ServiceInterface
    {
        if (!isset($this->_servicesHandled[$path])) {
            $path = preg_replace('/#\d+$/', '', $path);
            $wayPoints = explode('.', $path);
            $first = true;
            /**
             * @var ServiceInterface|ServiceContainedTrait|DbStorageTrait $component
             */
            $component = null;
            foreach ($wayPoints as $componentName) {
                if (true === $first) {
                    $component = $this->$componentName;
                    $first = false;
                } else {
                    $component = $component->$componentName;
                }
            }
            $this->_servicesHandled[$path] = $component;
        }
        return $this->_servicesHandled[$path];
    }

    protected function _prepareDefaultFields(DbCursorInterface $cursor, array $defaultFields)
    {
        $fields = [];
        foreach ($defaultFields as $key => $value) {
            if (!is_numeric($key)) {
                /**
                 * @var ServiceInterface|ServiceContainedTrait|DbStorageTrait $component
                 */
                $component = $this->_getComponentFromPath($key);
                $aliasCollection = $component->alias;
                if (preg_match('/#(\d+)$/', $key, $match)) {
                    $aliasCollection .= $match[1];
                }
                if (is_array($value)) {
                    foreach ($value as $fieldName => $aliasField) {
                        if (is_numeric($fieldName)) {
                            $fieldName = $aliasField;
                            $fields[] = "$aliasCollection.$fieldName";
                        } else {
                            $fields["$aliasCollection.$fieldName"] = $aliasField;
                        }
                    }
                } else {
                    $fields[] = "$aliasCollection.$value";
                }
            } else {
                is_array($value) ? $fields = array_merge($fields, $value) : $fields[] = $value;
            }
        }
        $cursor->fields($fields);
    }

    protected function _prepareDefaultJoins(DbCursorInterface $cursor, array $defaultJoins)
    {
        foreach ($defaultJoins as $service => $criteria) {
            /**
             * @var ServiceInterface|ServiceContainedTrait|DbStorageTrait $component
             */
            $component = $this->_getComponentFromPath($service);
            $alias = $component->alias;
            if (preg_match('/#(\d+)$/', $service, $match)) {
                $alias .= $match[1];
            }
            $ownerAlias = $this->alias;
            list($fieldChild, $fieldOwner) = $criteria;
            $cursor->join([$component->collectionName => $alias], ["$alias.$fieldChild" => "$ownerAlias.$fieldOwner"]);
        }
    }

    protected function _prepareDefaultLeft(DbCursorInterface $cursor, array $defaultLeft)
    {
        foreach ($defaultLeft as $service => $criteria) {
            /**
             * @var ServiceInterface|ServiceContainedTrait|DbStorageTrait $component
             */
            $component = $this->_getComponentFromPath($service);
            $alias = $component->alias;
            if (preg_match('/#(\d+)$/', $service, $match)) {
                $alias .= $match[1];
            }
            $ownerAlias = $this->alias;
            list($fieldChild, $fieldOwner) = $criteria;
            $cursor->left([$component->collectionName => $alias], ["$alias.$fieldChild" => "$ownerAlias.$fieldOwner"]);
        }
    }

    protected function _prepareDefaultRight(DbCursorInterface $cursor, array $defaultRight)
    {
        foreach ($defaultRight as $service => $criteria) {
            /**
             * @var ServiceInterface|ServiceContainedTrait|DbStorageTrait $component
             */
            $component = $this->_getComponentFromPath($service);
            $alias = $component->alias;
            if (preg_match('/#(\d+)$/', $service, $match)) {
                $alias .= $match[1];
            }
            $ownerAlias = $this->alias;
            list($fieldChild, $fieldOwner) = $criteria;
            $cursor->right([$component->collectionName => $alias], ["$alias.$fieldChild" => "$ownerAlias.$fieldOwner"]);
        }
    }

    protected function _prepareDefaultWhere(DbCursorInterface $cursor, $defaultWhere)
    {
        $cursor->where($defaultWhere);
    }
}
