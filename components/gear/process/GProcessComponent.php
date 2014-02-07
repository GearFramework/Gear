<?php

namespace gear\components\gear\process;
use \gear\Core;
use \gear\library\GComponent;
use \gear\library\GException;
use \gear\library\GModel;

/** 
 * Компонент обслуживающий процессы
 * 
 * @package Gear Framework
 * @component ProcessComponent
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GProcessComponent extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'defaultProcess' => 'index',
    );
    protected static $_init = false;
    protected $_processes = array();
    protected $_currentProcess = null;
    /* Public */
    
    /**
     * Установка описания процессов
     * 
     * @access public
     * @param array $processes
     * @return void
     */
    public function setProcesses(array $processes) { $this->_processes = $processes; }
    
    /**
     * Получение описания процессов
     * 
     * @access public
     * @return array
     */
    public function getProcesses() { return $this->_processes; }
    
    /**
     * Исполнение процесса
     * 
     * @access public
     * @param mixed $request
     * @throws ProcessComponentException
     * @return mixed
     */
    public function exec($request = array())
    {
        try
        {
            if (!isset($request['e']))
            {
                $processName = $this->i('defaultProcess');
                if (!$processName)
                    $this->e('Unknown process');
            }
            else
                $processName = $request['e'];
            if (isset($this->_processes[$processName]) && is_array($this->_processes[$processName]) && isset($this->_processes[$processName]['class']))
            {
                list($class, $config, $properties) = Core::getRecords($this->_processes[$processName]);
                $properties['name'] = $processName;
                $this->_currentProcess = new $class($properties);
            }
            else
            if (isset($this->_processes[$processName]) && $this->_processes[$processName] instanceof \Closure)
                $this->_currentProcess = $this->_processes[$processName];
            else
            {
                $routes = explode('/', $processName);
                $nums = count($routes);
                if ($nums == 1)
                    $class = Core::app()->getNamespace() . '\\process\\G' . ucfirst($processName);
                else
                if ($nums == 2)
                {
                    if ($processName[0] === '/')
                        $class = '\\' . $routes[0] . '\\process\\G' . ucfirst($routes[1]);
                    else
                        $class = Core::app()->getNamespace() . '\\modules\\' . $routes[0] . '\\process\\G' . ucfirst($routes[1]);
                }
                else
                if ($nums >= 3)
                    $class = '\\' . $routes[0] . '\\modules\\' . $routes[1] . '\\process\\G' . ucfirst($routes[2]);
                $this->_currentProcess = new $class(isset($this->_processes[$processName]) ? $this->_processes[$processName] : array());
            }
            return call_user_func
            (
                $this->_currentProcess instanceof \Closure ? $this->_currentProcess : array($this->_currentProcess, 'entry'), 
                $request
            );
        }
        catch(GException $e)
        {
            if (Core::app()->hasHttp())
                header('HTTP/1.0 404 Not Found', true, 404);
            else
                echo 'Process not found'.
            exit(404);
        }
    }
    
    /**
     * Возвращает текущий исполняемый процесс
     * 
     * @access public
     * @return \gear\models\GProcess
     */
    public function getProcess() { return $this->_currentProcess; }
    
    /**
     * Установка текущего процесса
     * 
     * @access public
     * @return void
     */
    public function setProcess($process)
    {
        if ($process instanceof \gear\interfaces\IProcess || is_callable($process))
            $this->_currentProcess = $process;
        else
            $this->e('Invalid process');
    }
}

/** 
 * Класс исключений компонента
 * 
 * @package Gear Framework
 * @component ProcessComponent
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class ProcessComponentException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
