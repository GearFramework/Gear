<?php

namespace Gear\Traits;

use Gear\Core;
use Gear\Interfaces\IDbCollection;
use Gear\Interfaces\IDbConnection;
use Gear\Interfaces\IDbCursor;
use Gear\Interfaces\IDbDatabase;
use Gear\Interfaces\IModel;
use Gear\Interfaces\IService;

/**
 * Трейт компонентов для выполнения операций с моделями
 * в базах данных
 *
 * @package Gear Framework
 *
 * @property string alias
 * @property string collectionName
 * @property string connectionName
 * @property IDbCursor cursor
 * @property string dbName
 * @property IDbCursor defaultCursor
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TDbStorage
{
    protected $_servicesHandled = [];

    /**
     * Добавление модели в набор (сохранение в коллекции-таблице в базе данных)
     *
     * @param IModel|array of IModel $model
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function add($model): int
    {
        return $this->selectCollection()->insert($model);
    }

    /**
     * Выборка всех моделей из коллекции
     *
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function all(): iterable
    {
        return $this->getIterator($this->getDefaultCursor());
    }

    /**
     * Выборка модели по значению первичного ключа
     *
     * @param int|string $pkValue
     * @return \Gear\Interfaces\IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function byPk($pkValue)
    {
        $class = $this->_factoryProperties['class'];
        $result = $this->selectCollection()->findOne([$class::$primaryKeyName => "'$pkValue'"]);
        return $result ? $this->factory($result) : $result;
    }

    /**
     * Возвращает количество элементов в коллекции, удовлетворяющих
     * критерию
     *
     * @param array $criteria
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function count($criteria = []): int
    {
        return $this->selectCollection()->find($criteria)->count();
    }

    /**
     * Возвращает true, если указанный в критерии элемент существует в коллекции
     *
     * @param array $criteria
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exists($criteria = []): bool
    {
        return $this->selectCollection()->exists($criteria);
    }
    /**
     * Поиск моделей по указанному критерию
     *
     * @param array|string $criteria
     * @param array|string $fields
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function find($criteria = [], $fields = []): iterable
    {
        return $this->getIterator($this->selectCollection()->find($criteria, $fields));
    }

    /**
     * Поиск модели, соответствующей указанному критерию
     *
     * @param array|string $criteria
     * @return \Gear\Interfaces\IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function findOne($criteria = [])
    {
        $result = $this->selectCollection()->findOne($criteria);
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
     * @return IDbConnection
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection
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
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): IDbCursor
    {
        return $this->selectCollection()->cursor;
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
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultCursor(): IDbCursor
    {
        $criteria = [];
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
     * Возвращает итератор со записями
     *
     * @param mixed $cursor
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator($cursor = null): iterable
    {
        if ($cursor instanceof \Iterator) {
            $cursor = $this->delegate($cursor);
        } else if (is_string($cursor)) {
            $cursor = $this->delegate($this->cursor->runQuery($cursor));
        } else {
            $cursor = $this->delegate($this->getDefaultCursor());
        }
        return $cursor;
    }

    /**
     * Удаление модели
     *
     * @param array|IModel|array of IModel $model
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($model)
    {
        $this->selectCollection()->remove($model);
    }

    /**
     * Сохранение модели
     *
     * @param array|IModel|array of IModel $model
     * @since 0.0.1
     * @version 0.0.1
     */
    public function save($model)
    {
        $this->selectCollection()->save($model);
    }

    /**
     * Выбор коллекции
     *
     * @param string $alias
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $alias = ""): IDbCollection
    {
        return $this->connection->selectCollection($this->dbName, $this->collectionName, $alias);
    }

    /**
     * Выбор базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(): IDbDatabase
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
     * @param IDbConnection $connection
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setConnection(IDbConnection $connection)
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
     * @param $model
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($model)
    {
        $result = 0;
        if ($model instanceof IModel) {
            if ($model->onBeforeUpdate()) {
                $result = $this->selectCollection()->update($model);
                $model->onAfterUpdate();
            }
        } else {
            $result = $this->selectCollection()->update($model);
        }
        return $result;
    }

    /**
     * @param string $path
     * @return IService
     * @throws \CoreException
     */
    protected function _getComponentFromPath(string $path): IService
    {
        if (!isset($this->_servicesHandled[$path])) {
            $path = preg_replace('/#\d+$/', '', $path);
            $wayPoints = explode('.', $path);
            $first = true;
            /**
             * @var IService|TServiceContained|TDbStorage $component
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

    protected function _prepareDefaultFields(IDbCursor $cursor, array $defaultFields)
    {
        $fields = [];
        foreach ($defaultFields as $key => $value) {
            if (!is_numeric($key)) {
                /**
                 * @var IService|TServiceContained|TDbStorage $component
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

    protected function _prepareDefaultJoins(IDbCursor $cursor, array $defaultJoins)
    {
        foreach ($defaultJoins as $service => $criteria) {
            /**
             * @var IService|TServiceContained|TDbStorage $component
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

    protected function _prepareDefaultLeft(IDbCursor $cursor, array $defaultLeft)
    {
        foreach ($defaultLeft as $service => $criteria) {
            /**
             * @var IService|TServiceContained|TDbStorage $component
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

    protected function _prepareDefaultRight(IDbCursor $cursor, array $defaultRight)
    {
        foreach ($defaultRight as $service => $criteria) {
            /**
             * @var IService|TServiceContained|TDbStorage $component
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

    protected function _prepareDefaultWhere(IDbCursor $cursor, $defaultWhere)
    {
        $cursor->where($defaultWhere);
    }
}
