<?php

namespace gear\library\io;
use gear\Core;
use gear\library\GModel;
use gear\library\GException;

/**
 * Абстрактный класс ввода/вывода
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
abstract class GIo extends GModel
{
    /* Const */
    const UNKNOWN = 0;
    const FIFO = 1;
    const CHAR = 2;
    const DIR = 3;
    const BLOCK = 4;
    const LINK = 5;
    const FILE = 6;
    const SOCKET = 7;
    /* Private */
    /* Protected */
    protected $_handler = null;
    protected $_types =
    [
        self::UNKNOWN => 'unknown',
        self::FIFO => 'fifo',
        self::CHAR => 'char',
        self::DIR => 'dir',
        self::BLOCK => 'block',
        self::LINK => 'link',
        self::FILE => 'file',
        self::SOCKET => 'socket',
    ];
    /* Public */
    
    /**
     * Закрывает ввод/вывод при уничтожении объекта
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
        if ($this->isOpened())
            $this->close();
    }
    
    /**
     * Возвращает true если ввод/ввывод открыт
     * 
     * @access public
     * @return boolean
     */
    public function isOpened() { return $this->_handler ? true : false; }

    /**
     * Открытие ввода/вывода
     * 
     * @access public
     * @return void
     */
    abstract public function open();

    /**
     * Чтение
     * 
     * @access public
     * @return void
     */
    abstract public function read();

    /**
     * Запись
     * 
     * @access public
     * @return void
     */
    abstract public function write();

    /**
     * Закрытие ввода/вывода
     * 
     * @access public
     * @return void
     */
    abstract public function close();
}

/**
 * Исключения операций ввода/вывода
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
class IoException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
