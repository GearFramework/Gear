<?php

class ArrayHelper
{
    public static function IsAssoc($array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
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
        if (is_string($criteria) || is_numeric($criteria)) {
            $result = $criteria;
        } else if (is_array($criteria)) {
            $result = [];
            foreach($criteria as $left => $right) {
                if (is_numeric($left)) {
//                echo "LOG > Found left numeric operand : [$left]\n";
                    if (is_array($right)) {
//                    echo "LOG > Found groupping : [()]\n";
                        $result[] = ($result ? " $logic " : "") . '(' . $this->_prepareCriteria($right, $logic, $op, $eq) . ')';
                    } else {
//                    echo "LOG > Found equals : [$left $eq $right]\n";
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
//                echo "LOG > Found left operand : [$left]\n";
                    $left = $this->_prepareOperand($left, true);
                    if (is_array($right)) {
                        if (!ArrayHelper::isAssoc($right)) {
                            $result[] = ($result ? $logic : "") . " $left IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                        } else {
                            $result[] = ($result ? $logic : "") . " " . $this->_prepareCriteria($right, $logic, $left, $eq);
                        }
                    } else {
//                    echo "LOG > Found equals 2 : [$left $eq $right]\n";
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

    private function _prepareOperand($operand, $strictLeft = false)
    {
        if (!is_numeric($operand)) {
//            echo "LOG > Operand is not numeric [$operand]\n";
            if ($operand === null || preg_match('/^null$/i', $operand)) {
//                echo "LOG > Operand is NULL [$operand]\n";
                $operand = 'NULL';
            } else if (preg_match('/^[a-z0-9_]+\.[a-z0-9_]+$/i', $operand)) {
//                echo "LOG > Operand is column.alias [$operand]\n";
                $rec = explode('.', $operand);
                $operand = '`' . implode('`.`', $rec) . '`';
            } else if (strpos($operand, ' AS ') !== false) {
//                echo "LOG > Operand is column as alias [$operand]\n";
                $operand = preg_replace('/\s{2,}/', ' ', $operand);
                list($left, $alias) = explode(' AS ', $operand);
                $operand = (preg_match('/^[A-Z0-9_]+$/i', $left) ? "`$left`" : $left) . " AS `$alias`";
            } else if ($operand[0] === ':') { // Column in table $operand == ':id'
//                echo "LOG > Operand is column [$operand]\n";
                $operand = '`' . substr($operand, 1) . '`';
            } else if ($operand[0] === '$') { // Function $operand == '$SUM(price)'
//                echo "LOG > Operand is function [$operand]\n";
                $operand = substr($operand, 1);
            } else if (!preg_match('/^[a-z0-9_]+\(.*\)$/i', $operand)) {
                $operand = $strictLeft ? "`$operand`" : false;
            }
        } else {
            $operand = false;
        }
        return $operand;
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
