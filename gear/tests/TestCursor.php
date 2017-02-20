<?php

class Cursor
{
    private $_logics = ['$or' => 'OR', '$and' => 'AND'];
    private $_eqs = ['$lt' => '<', '$gt' => '>', '$ne' => '<>', '$lte' => '<=', '$gte' => '>='];
    private $_funcs = ['$isn' => 'IS NULL', '$isnn' => 'IS NOT NULL'];

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
    private function _prepareCriteria(array $criteria, $logic = 'AND', $op = null, $eq = '='): string
    {
        $result = [];
        foreach($criteria as $left => $right) {
            if (is_numeric($left)) {
                $result[] = '(' . $this->_prepareCriteria($right, $logic) . ')';
            } else if (in_array($left, array_keys($this->_logics))) {
                $result[] = $this->_logics[$left] . ' ' . $this->_prepareCriteria($right, $this->_logics[$left]);
            } else if (in_array($left, array_keys($this->_eqs))) {
                if (!$op) {
                    $result[] = $this->_prepareCriteria($right, $logic, null, $eq); // ['$lt' => ['a' => 3]]
                } else {
                    $result[] = "$op " . $this->_eqs[$left] . " " . $this->_prepareValue($right); // ['a' => ['$lt' => 3]]
                }
            } else if ($left === '$in') {
                $result[] = ($result ? $logic : "") . " $op IN (" . implode(', ', $this->_prepareValue($right)) . ')';
            } else if ($left === '$nin') {
                $result[] = ($result ? $logic : "") . " $op NOT IN (" . implode(', ', $this->_prepareValue($right)) . ')';
            } else {
                if (is_array($right)) {
                    if (!ArrayHelper::isAssoc($right)) {
                        $result[] = ($result ? $logic : "") . " $left IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                    } else {
                        $result[] = ($result ? $logic : "") . " " . $this->_prepareCriteria($right, $logic, $left);
                    }
                } else {
                    $result[] = ($result ? $logic : "") . " $left $eq " . $this->_prepareValue($right);
                }
            }
        }
        return implode(" ", $result);
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

    private function _prepareOperand($operand)
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
            } else {
                $operand = false;
            }
        } else {
            $operand = false;
        }
        return $operand;
    }

}

$cursor = new Cursor();
$result = $cursor->find(['a' => 1]);
echo (is_array($result) ? print_r($result, 1) : $result) . "\n";
$result = $cursor->find(['a' => 1, 'b' => ':a']);
echo (is_array($result) ? print_r($result, 1) : $result) . "\n";
