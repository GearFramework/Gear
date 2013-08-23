<?php

namespace gear\components\gear\db\mysql;
use \gear\Core;
use \gear\library\db\GDbConnection;
use \gear\library\GException;

/** 
 * Класс компонента выполняющего подключение к MySQL
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 09.05.2013
 */
class GMysql extends GDbConnection
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'autoConnect' => true,
        'classItem' => '\\gear\\components\\gear\\db\\mysql\\GMysqlDatabase',
        'plugins' => array
        (
            'trace' => array
            (
                'class' => '\\gear\\plugins\\gear\\GDbTracer',
                'log' => 'logs\\mysql-%Y-%m-%d.log',
                'explain' => true,
                'rotate' => true,
            ),
        ),
    );
    protected static $_init = false;
    protected $_handler = null;
    protected $_properties = array
    (
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
    );
    protected $_current = null;
    /* Public */
    
    /**
     * Подключение к серверу MySQL
     * 
     * @access public
     * @return $this
     */
    public function connect()
    {
        $this->_handler = mysqli_connect($this->host, $this->username, $this->password);
        return $this;
    }
    
    /**
     * Закрытие соединения с сервером MySQL
     * 
     * @access public
     * @return $this
     */
    public function close()
    {
        if ($this->isConnected())
            mysqli_close($this->_handler);
        return $this;
    }
    
    /**
     * Перемотка в начало списка баз данных
     * 
     * @access public
     * @return GMySqlDatabase
     */
    public function rewind()
    {
        $this->event('onBeforeRewind');
        $this->reconnect();
        $result = mysqli_query($this->_handler, 'SHOW DATABASES');
        $class = $this->i('classItem');
        while($db = mysqli_fetch_row($result))
        {
            $this->_items[$db[0]] = new $class(array('owner' => $this, 'name' => $db[0]));
        }
        $this->event('onAfterRewind');
        return $this->_current = reset($this->_items);
    }
}

/** 
 * Класс исключений компонента выполняющего подключение к MySQL
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 09.05.2013
 */
class MySqlException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
