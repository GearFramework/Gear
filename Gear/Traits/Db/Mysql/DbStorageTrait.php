<?php

namespace Gear\Traits\Db\Mysql;

use Gear\Components\Db\Mysql\GMySqlCursor;
use Gear\Core;
use Gear\Interfaces\DbCollectionInterface;
use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Interfaces\DbDatabaseInterface;
use Gear\Interfaces\DbStorageComponentInterface;
use Gear\Interfaces\ModelInterface;
use Gear\Interfaces\ObjectInterface;
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
    protected $_cursor = null;

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
        $collection = $this->selectCollection();
        $collection->insert($model);
        return $collection->lastInsertId();
    }

    public function affected(): int
    {
        return $this->cursor->affected();
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
     * @return ModelInterface|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function byPk($pkValue): ?ObjectInterface
    {
        return $this->findOne(["{$this->alias}.{$this->primaryKeyName}" => $pkValue]);
    }

    /**
     * Возвращает количество элементов в коллекции, удовлетворяющих
     * критерию и ограниченная LIMIT
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.2
     */
    public function count(): int
    {
        return $this->cursor->count();
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
        return $this->cursor->reset()->exists($criteria);
    }

    /**
     * Поиск моделей по указанному критерию
     *
     * @param array|string|DbCursorInterface $criteria
     * @param array|string $fields
     * @param bool $useDefaults
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function find($criteria = [], $fields = [], bool $useDefaults = true): DbStorageComponentInterface
    {
        $this->reset();
        if (is_array($criteria)) {
            $criteria = array_merge($this->getDefaultWhere(), $criteria);
        }
        $this->cursor->find($criteria, $fields);
        if ($useDefaults) {
            $this->sort($this->getDefaultSort());
        }
        /** @var DbStorageComponentInterface $this */
        return $this;
    }

    /**
     * Поиск модели, соответствующей указанному критерию
     *
     * @param array|string|DbCursorInterface $criteria
     * @param array $fields
     * @return \Gear\Interfaces\ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function findOne($criteria = [], $fields = []): ?ObjectInterface
    {
        $this->reset();
        $criteria = array_merge($this->getDefaultWhere(), $criteria);
        $result = $this->cursor->findOne($criteria, $fields);
        /** @var ObjectInterface $result */
        return $result ? $this->factory($result) : $result;
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
        if (!$this->_cursor) {
            $this->_cursor = $this->selectCollection($this->alias)->cursor;
        }
        return $this->_cursor;
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
        $cursor = $this->cursor;
        if (isset($this->_defaultParams['where'])) {
            $this->_prepareDefaultWhere($cursor, $this->_defaultParams['where']);
        }
        if (isset($this->_defaultParams['fields'])) {
            $this->_prepareDefaultFields($cursor, $this->_defaultParams['fields']);
        }
        if (isset($this->_defaultParams['join'])) {
            $this->_prepareDefaultJoin($cursor, $this->_defaultParams['join']);
        }
        if (isset($this->_defaultParams['left'])) {
            $this->_prepareDefaultLeft($cursor, $this->_defaultParams['left']);
        }
        if (isset($this->_defaultParams['right'])) {
            $this->_prepareDefaultRight($cursor, $this->_defaultParams['right']);
        }
        if (isset($this->_defaultParams['sort'])) {
            $cursor->sort($this->_defaultParams['sort']);
        }
        if (isset($this->_defaultParams['limit'])) {
            $cursor->limit($this->_defaultParams['limit']);
        }
        return $cursor;
    }

    public function getDefaultSort(): array
    {
        return isset($this->_defaultParams['sort']) ? $this->_defaultParams['sort'] : [];
    }

    public function getDefaultWhere(): array
    {
        return isset($this->_defaultParams['where']) ? $this->_defaultParams['where'] : [];
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
            $cursor = $this->delegate(clone $cursor);
        } elseif (is_string($cursor)) {
            $cursor = $this->delegate($this->cursor->runQuery($cursor));
        } else {
            if ($this->isValidCursor()) {
                $cursor = $this->delegate(clone $this->cursor);
            } else {
                $cursor = $this->delegate(clone $this->getDefaultCursor());
            }
        }
        return $cursor;
    }

    /**
     * Возвращает значение PRIMARYKEY поля
     *
     * @param array|ModelInterface $object
     * @return mixed
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
        return null;
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
        return $this->_primaryKeyName;
    }

    public function isValidCursor(): bool
    {
        return $this->_cursor instanceof DbCursorInterface;
    }

    /**
     * Ограничение выборки
     *
     * @param mixed ...$limit
     * @return DbStorageComponentInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function limit(...$limit): DbStorageComponentInterface
    {
        $this->cursor->limit(...$limit);
        /** @var DbStorageComponentInterface $this */
        return $this;
    }

    /**
     * Возвращает количество элементов в коллекции, удовлетворяющих
     * критерию и не ограниченная LIMIT
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.2
     */
    public function realCount(): int
    {
        return $this->cursor->realCount();
    }

    /**
     * Удаление модели
     *
     * @param array|ModelInterface $model
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove($model): DbStorageComponentInterface
    {
        $this->selectCollection()->remove($model);
        /** @var DbStorageComponentInterface $this */
        return $this;
    }

    /**
     * Сброс результатов выполнения запроса
     *
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function reset(): DbStorageComponentInterface
    {
        $this->cursor->reset();
        $this->cursor = null;
        /** @var DbStorageComponentInterface $this */
        return $this;
    }

    /**
     * Сохранение модели
     *
     * @param array|ModelInterface $model
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function save($model): DbStorageComponentInterface
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
        /** @var DbStorageComponentInterface $this */
        return $this;
    }

    /**
     * Выбор коллекции
     *
     * @param string $alias
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectCollection(string $alias = ''): DbCollectionInterface
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
     * Установка текущего курсора коллекции
     *
     * @param DbCursorInterface|null $cursor
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setCursor(?DbCursorInterface $cursor)
    {
        $this->_cursor = $cursor;
    }

    /**
     * Сортировка
     *
     * @param array $sort
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sort(array $sort): DbStorageComponentInterface
    {
        $this->cursor->sort($sort);
        /** @var DbStorageComponentInterface $this */
        return $this;
    }

    /**
     * Обновление существующей модели
     *
     * @param $model
     * @param array|DbCursorInterface $criteria
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function update($model, $criteria = []): DbStorageComponentInterface
    {
        if ($model instanceof ModelInterface) {
            if ($model->onBeforeUpdate()) {
                $this->cursor->update($model);
                $model->onAfterUpdate();
            }
        } else {
            $this->cursor->update($model, $criteria);
        }
        /** @var DbStorageComponentInterface $this */
        return $this;
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

    protected function _prepareDefaultJoin(DbCursorInterface $cursor, array $defaultJoins)
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
