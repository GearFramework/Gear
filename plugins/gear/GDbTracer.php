<?php

namespace gear\plugins\gear;
use \gear\Core;
use \gear\library\GException;
use \gear\plugins\gear\GLog;

class GDbTracer extends GLog
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_properties = array
    (
        'log' => 'logs\\mysql-%Y-%m-%d.log',
        'explain' => false,
        'filename' => null,
        'rotate' => false,
    );
    /* Public */
    
    protected function _explain($query)
    {
        $explain = array();
        $cols = array();
        foreach($query->explain() as $row)
        {
            foreach($row as $name => $value)
            {
                if (!isset($cols[$name]))
                    $cols[$name] = strlen($name);
                $len = strlen($value);
                if ($cols[$name] < $len)
                    $cols[$name] = $len;
            }
            $explain[] = $row;
        }
        $data = array();
        foreach($cols as $name => $size)
        {
            $sub = $size - strlen($name);
            $data[] = $name . str_repeat(' ', $sub);
        }
        $data = str_repeat('-', array_sum($cols) + count($cols) - 1) . "\n" 
              . implode('|', $data) . "\n" 
              . str_repeat('-', array_sum($cols) + count($cols) - 1) . "\n";
        $records = array();
        foreach($explain as $row)
        {
            $values = array();
            foreach($row as $name => $value)
            {
                $size = $cols[$name];
                $sub = strlen($value) < $size ? $size - strlen($value) : 0;
                $values[] = $value . str_repeat(' ', $sub);
            }
            $records[] = implode('|', $values);
        }
        return $data .= implode("\n", $records);
    }

    public function trace($query)
    {
        if ($this->rotate || !$this->filename)
            $this->_processFileName();
        if (is_string($query))
            @file_put_contents($this->filename, '[' . date('d/m/Y H:i:s') . '] ' . $query . "\n", FILE_APPEND);
        else
        if (is_object($query))
        {
            @file_put_contents
            (
                $this->filename, 
                '[' . date('d/m/Y H:i:s') . '] Run query on ' . $query->getConnection()->host . '.' . $query->getDb()->name . '.' . $query->getCollection()->name . "\nQUERY :\n" . $query->getText() . "\n", 
                FILE_APPEND
            );
            if ($this->explain)
                @file_put_contents($this->filename, "EXPLAIN :\n" . $this->_explain($query) . "\n", FILE_APPEND);
        }
    }

    public function write($stringLog, array $params = array())
    {
        $this->trace($stringLog);
    }
}

class DbTracerException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
