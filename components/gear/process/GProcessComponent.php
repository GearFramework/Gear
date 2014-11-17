<?php

namespace gear\components\gear\process;
use gear\Core;
use gear\library\GComponent;
use gear\library\GException;
use gear\library\GModel;

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
    protected static $_config = array();
    protected static $_init = false;
    protected $_defaultProcess = 'index';
    protected $_processes = array();
    protected $_currentProcess = null;
    /* Public */
    
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
            $args = func_get_args();
            $nums = func_num_args();
            if (!$nums)
                $this->_currentProcess = $this->_prepareProcess(array());
            else
            if ($nums >= 1)
            {
                if ($args[0] instanceof \gear\interfaces\IProcess || $args[0] instanceof \Closure)
                {
                    $request = isset($args[1]) && is_array($args[1]) ? $args[1] : array();
                    $this->_currentProcess = $args[0];
                }
                else
                {
                    $request = is_array($args[0]) ? $args[0] : array();
                    $this->_currentProcess = $this->_prepareProcess($request);
                }
            }
            return call_user_func
            (
                $this->_currentProcess instanceof \Closure ? $this->_currentProcess : array($this->_currentProcess, 'entry'), 
                $request
            );
        }
        catch(GException $e)
        {
            $this->event('onProcessNotFound', $e, $request);
            if (Core::app()->hasHttp())
            {
                header('HTTP/1.0 404 Not Found', true, 404);
                echo $e->getMessage();
            }
            else
                echo $e->getMessage();
            exit(404);
        }
    }
    
    /**
     * Получение процесса исходя из запроса пользователя
     * 
     * @access protected
     * @param array $request
     * @return object of \gear\interfaces\IProcess or \Closure
     */
    protected function _prepareProcess(array $request)
    {
        $process = null;
        $processName = Core::app()->request->get('e');
        if (!$processName)
        {
            $processName = $this->getDefaultProcess();
            if (!$processName)
                $this->e('Unknown process');
        }
        $processes = $this->getProcesses();
        if (isset($processes[$processName]))
        {
            if ($processes[$processName] instanceof \Closure)
                $process = $processes[$processName];
            else
            if (is_array($processes[$processName]))
            {
                if (isset($processes[$processName]['class']))
                {
                    list($class, $config, $properties) = Core::getRecords($this->_processes[$processName]);
                    $properties['name'] = $processName;
                }
                else
                {
                    $class = $this->_prepareProcessClass($processName);
                    $properties = array_merge($processes[$processName], array('name' => $processName));
                }
            }
            else
            {
                $class = $this->_prepareProcessClass($processName);
                $properties = array('name' => $processName, 'params' => $processes[$processName]);
            }
        }
        else
        {
            $class = $this->_prepareProcessClass($processName);
            $properties = array('name' => $processName);
        }
        return $process ? $process : new $class($properties);
    }
    
    /**
     * Получение имя класса процесса
     * 
     * @access protected
     * @param string $processName
     * @return string
     */
    protected function _prepareProcessClass($processName)
    {
        $routes = explode('/', $processName);
        $nums = count($routes);
        if ($nums == 1)
            $class = Core::app()->getNamespace() . '\process\G' . ucfirst($processName);
        else
        if ($nums == 2)
        {
            if ($processName[0] === '/')
                $class = '\\' . $routes[0] . '\process\G' . ucfirst($routes[1]);
            else
                $class = Core::app()->getNamespace() . '\modules\\' . $routes[0] . '\process\G' . ucfirst($routes[1]);
        }
        else
        if ($nums >= 3)
            $class = '\\' . $routes[0] . '\modules\\' . $routes[1] . '\process\G' . ucfirst($routes[2]);
        return $class;
    }
    
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
     * Добавление описания процессов
     * 
     * @access public
     * @param array $processes
     * @return void
     */
    public function appendProcesses(array $processes) { $this->_processes = array_merge($this->_processes, $processes); }
    
    public function addProcess($name, $process)
    {
        $this->_processes[$name] = $process;
    }
    
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
    
    /**
     * Возвращает текущий исполняемый процесс
     * 
     * @access public
     * @return \gear\models\GProcess
     */
    public function getProcess() { return $this->_currentProcess; }
    
    /**
     * Установка названия процесса, исполняемого по-умолчанию
     * 
     * @access public
     * @param string $processName
     * @return void
     */
    public function setDefaultProcess($processName) { $this->_defaultProcess = $processName; }
    
    /**
     * Получение названия процесса, исполняемого по-умолчанию
     * 
     * @access public
     * @return string
     */
    public function getDefaultProcess() { return $this->_defaultProcess; }
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
