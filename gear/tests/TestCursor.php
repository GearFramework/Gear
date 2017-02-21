<?php

interface IModel {}

class ArrayHelper
{
    public static function IsAssoc($array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}

class Model implements IModel
{
    protected $_pk = 'a';
    public $a;
    public $b;

    public function __construct($props)
    {
        foreach($props as $n => $v) {
            $this->$n = $v;
        }
    }

    public function __get($name)
    {
        if ($name === 'pk') {
            return $this->_pk;
        }
    }

    public function props()
    {
        $p = [];
        foreach(['a', 'b'] as $n) {
            $p[$n] = $this->$n;
        }
        return $p;
    }

    public function vars()
    {

    }
}


class Cursor
{
    private $_logics = ['$or' => 'OR', '$and' => 'AND'];
    private $_eqs = ['$lt' => '<', '$gt' => '>', '$ne' => '<>', '$lte' => '<=', '$gte' => '>='];
    private $_sfuncs = ['$isn' => 'IS NULL', '$isnn' => 'IS NOT NULL'];
    private $_funcs = ['$like' => 'LIKE', '$nlike' => 'NOT LIKE', '$rlike' => 'RLIKE', '$nrlike' => 'NOT RLIKE', '$rxp' => 'REGEXP', '$nrgx' => 'NOT REGEXP'];

    public function find($criteria)
    {
        $result = $this->_prepareCriteria($criteria);
        return $result;
    }

    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @param string $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function escape($value)
    {
        return addslashes($value);
    }

    /**
     * Возвращает название коллекции (таблицы), дял которой создан курсор
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollectionName(): string
    {
        return 'table';
    }

    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     *
     * @param mixed $properties
     * @return integer|object
     * @since 0.0.1
     * @version 0.0.1
     */
    public function insert($properties)
    {
        $this->reset();
        $result = 0;
        if ($properties instanceof IModel) {
            $result = $properties;
            $properties = $result->props();
        } else if (is_object($properties)) {
            $result = $properties;
            $properties = [];
            foreach(get_class_vars(get_class($result)) as $name => $value) {
                $properties[$name] = $result->$name;
            }
        } else if (!is_array($properties)) {
            throw new \InvalidArgumentException('Invalid properties to insert');
        }
        list($names, $values) = $this->_prepareInsert($properties);
        $query = "INSERT INTO `" . $this->getCollectionName() . "` $names VALUES $values";
        return $query;
        $this->runQuery($query);
        return is_object($result) ? $result : $this->affected();
    }

    /**
     * Сброс результатов выполнения последнего запроса
     *
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reset()
    {
        return $this;
    }

    /**
     * Добавление в коллекцию новой записи. В случае совпадения
     * PRIMARY KEY происходит обновление указанных в $updates полей
     * записи
     * Возвращает количество затронутых полей
     *
     * @param array|IObject $properties
     * @param array $updates
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function save($properties, array $updates = [])
    {
        $this->reset();
        $result = 0;
        if ($properties instanceof IModel) {
            $result = $properties;
            $properties = $result->props();
        } else if (is_object($properties)) {
            $result = $properties;
            $properties = [];
            foreach(get_class_vars(get_class($result)) as $name => $value) {
                $properties[$name] = $result->$name;
            }
        } else if (!is_array($properties)) {
            throw new \InvalidArgumentException('Invalid properties to insert');
        }
        list($names, $values) = $this->_prepareInsert($properties);
        $query = "INSERT INTO `" . $this->getCollectionName() . "` $names VALUES $values";

        if (!$updates && is_object($result)) {
            $pk = $result->pk;
            if (!$pk) {
                throw self::exceptionCursor('Undefined primary key to save data');
            }
            $props = $result instanceof IObject ? $result->props() : get_class_vars(get_class($result));
            $properties = [];
            foreach($props as $name => $value) {
                if ($name !== $pk) {
                    $properties[] = $name;
                }
            }
            $updates = $this->_prepareUpdate($properties, $result);
        } else if ($updates) {
            if (is_object($result)) {
                $updates = $this->_prepareUpdate($updates, $result);
            } else if (\ArrayHelper::IsAssoc($updates)) {
                $updates = $this->_prepareUpdate($updates);
            } else {
                throw new \InvalidArgumentException('Invalid argument <updates> to save');
            }
        } else {
            throw new \InvalidArgumentException('Invalid argument <updates> to save');
        }
        $query .= " ON DUPLICATE KEY UPDATE " . $updates;
        return $query;
        $this->runQuery($query);
        return is_object($result) ? $result : $this->affected();
    }

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * $this->update([], ['a' => 2]);
     * $this->update(['a' => 2], ['a' => 3]);
     * $model = new \gear\library\GModel(['a' => 3]);
     * $this->update($model, ['a' => 4]);
     * $model->a = 5;
     * $this->update($model);
     * $model->a = 6;
     * $this->update($model, ['a']);
     *
     * @param array|object $criteria
     * @param array $properties
     * @return int|object
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($criteria, array $properties = [])
    {
        $this->reset();
        $result = $criteria;
        if (!is_array($criteria) && !is_string($criteria) && !is_object($criteria)) {
            throw new \InvalidArgumentException('Invalid argument <criteria> to update');
        }
        if (!$properties && !is_object($criteria)) {
            throw new \InvalidArgumentException('Invalid argument <properties> to update');
        }
        $properties = $this->_prepareUpdate($properties, $result);
        $criteria = $this->_prepareCriteria($result);
        $query = 'UPDATE `' . $this->getCollectionName() . '` SET ' . $properties . ($criteria ? ' WHERE ' . $criteria : '');
        return $query;
        $this->runQuery($query);
        return is_object($result) ? $result : $this->affected();
    }

    /**
     * $this->where(['a' => 2]);
     * $this->where(['$ne' => ['a' => 2]]);
     * $this->where(['a' => 'NOW()']);
     * $this->where(['a' => ':b']);
     * $this->where(['a' => ['&lt' => 2]]);
     * $this->where(['a' => [2, 3, 4]]);
     * $this->where(['a' => ['$lt' => 2, '$or' => 3]]);
     * $this->where(['a' => ['$lt' => 2, '$or' => ['$gt' => 7]]]);
     * $this->where([['a' => 2, '$and' => ['b' => 3]]]);
     *
     * @param array $criteria
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    private function _prepareCriteria($criteria, $logic = 'AND', $op = null, $eq = '='): string
    {
        if ($criteria instanceof IModel) {
            $pk = $criteria->pk;
            $result = $this->_prepareCriteria([$pk => $criteria->$pk]);
        } else if (is_string($criteria) || is_numeric($criteria)) {
            $result = $criteria;
        } else if (is_array($criteria)) {
            $result = [];
            foreach($criteria as $left => $right) {
                if (is_numeric($left)) {
                    if (is_array($right)) {
                        $result[] = ($result ? " $logic " : "") . '(' . $this->_prepareCriteria($right, $logic, $op, $eq) . ')';
                    } else {
                        if ($op) {
                            $result[] = ($result ? " $logic " : "") . "$left $eq " . $this->_prepareValue($right);
                        } else {
                            $result[] = ($result ? " $logic " : "") . $this->_prepareValue($right);
                        }
                    }
                } else if (in_array($left, array_keys($this->_logics))) { // AND, OR
                    /**
                     * SQL: Using AND, OR
                     */
                    $result[] = ($result ? $this->_logics[$left] : '') . ' ' . $this->_prepareCriteria($right, $this->_logics[$left], $op, $eq);
                } else if (in_array($left, array_keys($this->_eqs))) { // <, >, <=, =>, <>
                    /**
                     * SQL: Using operations: <, >, <>, <=, =>
                     */
                    if (!$op) {
                        $result[] = ($result ? " $logic " : "") . $this->_prepareCriteria($right, $logic, null, $this->_eqs[$left]); // ['$lt' => ['a' => 3]]
                    } else {
                        $result[] = ($result ? " $logic " : "") . " $op " . $this->_eqs[$left] . " " . $this->_prepareCriteria($right, $logic); // ['a' => ['$lt' => 3]]
                    }
                } else if ($left === '$in') {
                    /**
                     * SQL: COL IN (VAL1, VAL2, ... VALn)
                     */
                    $result[] = ($result ? $logic : "") . " $op IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                } else if ($left === '$nin') {
                    /**
                     * SQL: COL NOT IN (VAL1, VAL2, ... VALn)
                     */
                    $result[] = ($result ? $logic : "") . " $op NOT IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                } else if (in_array($left, array_keys($this->_sfuncs))) {
                    /**
                     * SQL: Using IS NULL, IS NOT NULL
                     */
                    $result[] = ($result ? $logic : "") . $this->_prepareOperand($right, true) . " " . $this->_sfuncs[$left];
                } else if (in_array($left, array_keys($this->_funcs))) {
                    /**
                     * SQL: Using LIKE, NOT LIKE, RLIKE, NOT RLIKE, REGEXP, NOT REGEXP
                     */
                    if (!$op) { // ['$like' => ['a' => 'pattern']]
                        list($operand, $pattern) = $right;
                        $result[] = ($result ? $logic : "") . $this->_prepareOperand($result, true) . " \"$pattern\"";
                    } else { // ['a' => ['$like' => 'pattern']]
                        $result[] = ($result ? $logic : "") . " $op " . $this->_funcs[$left] . " " . $this->_prepareValue($right);
                    }
                } else if ($left === '$bw') {
                    /**
                     * SQL: COL BETWEEN VAL1 AND VAL2
                     */
                    if (!$op) {
                        // ['$bw' => ['a' => [1, 10]]]
                        $operand = $this->_prepareOperand(key($right), true);
                        $values = $this->_prepareValue(current($right));
                        $result[] = ($result ? $logic : "") . " $operand BETWEEN " . implode(' AND ', $values);
                    } else {
                        // ['a' => ['$bw' => [1 => 10]]]
                        $right = $this->_prepareValue($right);
                        $result[] = ($result ? $logic : "") . " $op BETWEEN " . implode(' AND ', $right);
                    }
                } else if ($left[0] === '$') {
                    /**
                     * SQL: Using functions MAX(), MIN(), DATE(), NOW() and etc.
                     *
                     * ['$MAX' => ':a']
                     * ['$DATE_SUB' => ['NOW()', 'INTERVAL 1 DAYS']]
                     */
                    $left = substr($left, 1);
                    $right = $this->_prepareValue($right);
                    if (is_array($right)) {
                        $right = implode(', ', $right);
                    }
                    $result[] = ($result ? $logic : "") . " " . ($op ? " $op " . ($eq ? $eq : '') : '') . " $left($right)";
                } else {
                    $left = $this->_prepareOperand($left, true);
                    if (is_array($right)) {
                        if (!\ArrayHelper::isAssoc($right)) {
                            $result[] = ($result ? $logic : "") . " $left IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                        } else {
                            $result[] = ($result ? $logic : "") . " " . $this->_prepareCriteria($right, $logic, $left, $eq);
                        }
                    } else {
                        $right = $this->_prepareValue($right);
                        $result[] = ($result ? $logic : "") . " $left " . ($right === 'NULL' ? 'IS' : $eq) . " $right";
                    }
                }
            }
            $result = trim(implode(" ", $result));
        } else {
            throw new \InvalidArgumentException('Invalid arguments criteria to find');
        }
        return $result;
    }

    private function _prepareValue($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->_prepareValue($val);
            }
            unset($val);
        } else if ($value === null || preg_match('/^null$/i', $value)) {
            $value = 'NULL';
        } else if (($operand = $this->_prepareOperand($value))) {
            $value = $operand;
        } else {
            $value = "'" . $this->escape($value) . "'";
        }
        return $value;
    }

    /**
     * Возвращает массив подготовленных полей и данных для вставки
     *
     * @param array $properties
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    private function _prepareInsert(array $properties): array
    {
        /**
         * \ArrayHelper это алиас класса \gear\helpers\HArray
         */
        if (\ArrayHelper::isAssoc($properties)) {
            $names = array_keys($properties);
            foreach($properties as &$value) {
                $value = '"' . $this->escape($value) . '"';
            }
            unset($value);
            $properties = '(' . implode(', ', $properties) . ')';
        } else {
            $first = reset($properties);
            if (is_object($first)) {
                $names = array_keys($first instanceof IModel ? $first->props() : get_object_vars($first));
            } else {
                $names = array_keys($first);
            }
            foreach($properties as $index => $p) {
                if (is_object($p)) {
                    $p = $p instanceof IModel ? $p->props() : get_object_vars($p);
                }
                foreach($p as &$value) {
                    $value = '"' . $this->escape($value) . '"';
                }
                unset($value);
                $properties[$index] = '(' . implode(', ', $p) . ')';
            }
            $properties = implode(', ', $properties);
        }
        $names = '(`' . implode('`, `', $names) . '`)';
        return [$names, $properties];
    }

    private function _prepareOperand($operand, $strictLeft = false)
    {
        if (!is_numeric($operand)) {
            if ($operand === null || preg_match('/^null$/i', $operand)) {
                $operand = 'NULL';
            } else if (preg_match('/^[a-z0-9_]+\.[a-z0-9_]+$/i', $operand)) {
                $rec = explode('.', $operand);
                $operand = '`' . implode('`.`', $rec) . '`';
            } else if (strpos($operand, ' AS ') !== false) {
                $operand = preg_replace('/\s{2,}/', ' ', $operand);
                list($left, $alias) = explode(' AS ', $operand);
                $operand = (preg_match('/^[A-Z0-9_]+$/i', $left) ? "`$left`" : $left) . " AS `$alias`";
            } else if ($operand[0] === ':') { // Column in table $operand == ':id'
                $operand = '`' . substr($operand, 1) . '`';
            } else if ($operand[0] === '$') { // Function $operand == '$SUM(price)'
                $operand = substr($operand, 1);
            } else if (!preg_match('/^[a-z0-9_]+\(.*\)$/i', $operand)) {
                $operand = $strictLeft ? "`$operand`" : false;
            }
        } else {
            $operand = false;
        }
        return $operand;
    }

    private function _prepareUpdate($properties, $source = null)
    {
        $result = [];
        if ($source && is_object($source)) {
            if (!$properties) {
                $properties = array_keys($source instanceof IModel ? $source->props() : get_class_vars(get_class($source)));
            }
            $pk = $source->pk;
            if (\ArrayHelper::IsAssoc($properties)) {
                foreach($properties as $name => $value) {
                    if ($pk && $pk !== $name) {
                        $source->$name = $value;
                        $result[] = "`$name` = '" . $this->escape($source->$name) . "'";
                    }
                }
            } else {
                foreach($properties as $name) {
                    if ($pk && $pk !== $name) {
                        $result[] = "`$name` = '" . $this->escape($source->$name) . "'";
                    }
                }
            }
        } else {
            foreach($properties as $name => $value) {
                $result[] = "`$name` = " . $this->_prepareValue($value);
            }
        }
        return implode(', ', $result);
    }

}

$cursor = new Cursor();
$result = $cursor->find(['a' => 1]);
echo '1. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => 1, 'b' => ':a']);
echo '2. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find([['a' => 2, 'b' => 3]]);
echo '3. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find([['a' => ['$lt' => 2], 'b' => 3]]);
echo '4. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find([['a' => 3, '$or' => ['a' => 10]], 'c' => 4]);
echo '5. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => 'SUM(b)']);
echo '6. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => '$SUM(b)']);
echo '7. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => [1, 2, 3]]);
echo '8. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['SUM(a)' => [1, 2, 3]]);
echo '9. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => ['$nin' => [1, 2, 3]]]);
echo '10. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['$isn' => ':a']);
echo '11. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['$isnn' => 'a']);
echo '12. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => null]);
echo '13. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => 'null']);
echo '14. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find('a = 1');
echo '15. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a.b' => 1]);
echo '16. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => 1, '$or' => [['$and' => ['a' => 1, 'b' => 2]]]]);
echo '17. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['b' => 1,'$gt' => ['a' => 1]]);
echo '18. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => ['$bw' => [1, 10]]]);
echo '19. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['$bw' => ['a' => [1, 10]]]);
echo '20. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['$DATE_SUB' => ['NOW()', 'INTERVAL 1 DAYS']]);
echo '21. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => ['$DATE_SUB' => ['NOW()', 'INTERVAL 1 DAYS']]]);
echo '22. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => ['$gt' => ['$DATE_SUB' => ['NOW()', 'INTERVAL 1 DAYS']]]]);
echo '23. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find([['a' => 2, '$and' => ['b' => 3]]]);
echo '24. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";



$result = $cursor->save(['a' => 1, 'b' => 2], ['b' => 2]);
echo '25. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$model = new Model(['a' => 4, 'b' => 5]);
$result = $cursor->save($model);
echo '26. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$model->b = 10;
$result = $cursor->save($model);
echo '27. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$model->b = 10;
$result = $cursor->save($model, ['b']);
echo '28. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->save($model, ['b' => 20]);
echo '28. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$model->b = 50;
$result = $cursor->update($model);
echo '29. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->update($model, ['b']);
echo '30. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->update($model, ['b' => 60]);
echo '30. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->update(['a' => 1], ['b' => 70]);
echo '31. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$model = new Model(['a' => 0, 'b' => 5]);
$result = $cursor->insert($model);
echo '32. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->insert(['a' => 0, 'b' => 5]);
echo '33. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->insert([['a' => 0, 'b' => 5],['a' => 0, 'b' => 5]]);
echo '34. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
$model = new Model(['a' => 1, 'b' => 6]);
$model2 = new Model(['a' => 2, 'b' => 7]);
$result = $cursor->insert([$model, $model2]);
echo '35. ' . (is_array($result) ? print_r($result, 1) : $result) . "\n";
