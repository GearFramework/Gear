<?php

namespace gear\plugins\gear;
use gear\Core;
use gear\library\GPlugin;
use gear\library\GException;

/** 
 * Плагин ведения логов
 * 
 * @package Gear Framework
 * @plugin Log
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GLog extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_properties = array
    (
        'log' => 'logs\\log-%Y-%m-%d.log',
        'filename' => null,
        'rotate' => false,
    );
    /* Public */
    
    /**
     * Вызов метода write()
     * 
     * @access public
     * @return void
     */
    public function __invoke()
    {
        call_user_func_array(array($this, 'write'), func_get_args());
    }
    
    /**
     * Получение имени файла
     * 
     * @access protected
     * @return void
     */
    protected function _processFileName()
    {
        if ($this->log)
        {
            preg_match_all('#(\%[a-zA-Z]{1})#u', $this->log, $res);
            $format = $this->log;
            foreach($res[0] as $item)
            {
                $item = substr($item, 1, 1);
                if ($item == 'c')
                    $format = str_replace('%' . $item, get_class($this->_owner), $format);
                else
                    $format = str_replace('%' . $item, date($item), $format);
            }
            $this->filename = Core::resolvePath($format);
        }
    }

    /**
     * Запись лога в файл
     * 
     * @access public
     * @param string $stringLog
     * @return void
     */
    public function write($stringLog, array $params = array())
    {
        foreach($params as $name => $value)
            $stringLog = str_replace(':' . $name, $value, $stringLog);
        if ($this->rotate || !$this->filename)
            $this->_processFileName();
        @file_put_contents($this->filename, '[' . date('d/m/Y H:i:s') . '] ' . $stringLog . "\n", FILE_APPEND);
    }
}

/** 
 * Исключения плагина ведения логов
 * 
 * @package Gear Framework
 * @plugin Log
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class LogException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
