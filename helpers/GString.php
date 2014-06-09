<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GObject;

class GString extends GObject implements \Countable
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_value = '';
    protected $_encoding = 'UTF-8';
    /* Public */
    
    public function __toString() { return $this->_value; }
    
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
        return $this;
    }
    
    public function getEncoding() { return $this->_encoding; }
    
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }
    
    public function getValue() { return $this->_value; }
    
    public function count() { return $this->length(); }
    
    public function length() { return mb_strlen($this->_value, $this->_encoding); }
    
    public function at($index) { return $this->substr($index, 1); }
    
    public function chars()
    {
        if ($this->_encoding === 'UTF-8')
            return preg_split('//u', $this->_value, 0, PREG_SPLIT_NO_EMPTY);
        else
        {
            $chars = array();
            $len = $this->length();
            for($index = 0; $index < $len; ++ $index)
                $chars[] = mb_substr($this->_value, $index, 1, $this->_encoding);
            return $chars;
         } 
    }
    
    public function explode($delimiter) { return explode($delimiter, $this->_value); }
    
    public function implode($delimiter, array $values) 
    { 
        $this->_value = implode($delimiter, $values); 
        return $this;
    }
    
    /**
     * Переводит строку в нижний регистр
     * 
     * @access public
     * @return $this
     */
    public function toLower()
    {
        $this->_value = mb_strtolower($this->_value, $this->_encoding);
        return $this;
    }
    
    /**
     * Переводит строку в верхний регистр
     * 
     * @access public
     * @return $this
     */
    public function toUpper()
    {
        $this->_value = mb_strtoupper($this->_value, $this->_encoding);
        return $this;
    }
    
    /**
     * Возвращает подстроку
     * 
     * @access public
     * @param integer $position
     * @param integer $length
     * @return object
     */
    public function substr($position, $length = 0)
    {
        return new self(array
        (
            'value' => mb_substr($this->_value, $position, $length, $this->_encoding), 
            'encoding' => $this->_encoding
        ));
    }
    
    public function pad($pad, $length, $type = STR_PAD_RIGHT)
    {
        
    }
    
    public function trim($chars = null)
    {
        
    }
    
    public function rtrim($chars = null)
    {
        
    }
    
    public function ltrim($chars = null)
    {
        
    }
    
    public function search($pattern)
    {
        
    }
    
    public function replace($pattern, $replace)
    {
        
    }

    /**
     * Возвращает строку преобразованную в другую кодировку
     * 
     * @access public
     * @param string $encode
     * @return object
     */
    public function conv($encode) 
    { 
        return new self(array
        (
            'value' => iconv($this->_encoding, $encode, $this->_value), 
            'encoding' => $this->_encoding
        )); 
    }
    
    public function isEmpty($ignoreSpaces = false)
    {
        $string = !$ignoreSpaces ? $this->_value : trim($this->_value);
        return empty($string); 
    }
    
    public function onConstructed()
    {
        parent::onConstructed();
        mb_internal_encoding($this->_encoding);
        return true;
    }
}
