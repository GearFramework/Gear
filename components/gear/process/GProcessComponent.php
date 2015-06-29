<?php

namespace gear\components\gear\process;
use gear\Core;
use gear\interfaces\IProcess;
use gear\library\GComponent;
use gear\library\GException;

/** 
 * Компонент обслуживающий процессы
 *
 * @package Gear Framework
 * @component ProcessComponent
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.3.x
 * @release 1.0.0
 */
class GProcessComponent extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    /* Настройки компонента */
    protected static $_config = array();
    protected static $_init = false;
    /* Объект возвращающий параметры запроса */
    protected $_request = null;
    /* Имя процесса исполняемого по-умолчанию */
    protected $_defaultProcess = 'index';
    /* Список установленных процессов */
    protected $_processes = array();
    /* Текущий исполняемый процесс */
    protected $_currentProcess = null;
    /* Public */


    /**
     * Установка объекта отдающего параметры запроса
     *
     * @access public
     * @param object $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Получение объекта отдающего параметры запроса
     *
     * @access public
     * @return null|object
     */
    public function getRequest() { return $this->_request; }

    /**
     * Исполнение процесса
     *
     * @access public
     * @param null|object|\Closure $process
     * @param null|object $request
     * @return mixed
     */
    public function exec($process = null, $request = null)
    {
        try
        {
            if ($request && !is_object($request))
                $request = Core::app()->request;
            $this->request = $request;
            if ($process && ($process instanceof \gear\interfaces\IProcess || $process instanceof \Closure))
                $this->_currentProcess = $process;
            else
                $this->_currentProcess = $this->_routing();
            return $this->_currentProcess instanceof \Closure
                 ? call_user_func($this->_currentProcess, $this->request) : $this->_currentProcess->entry($this->request);
        }
        catch(GException $e)
        {
            $this->event('onProcessNotFound', $e);
            exit(404);
        }
    }
    
    /**
     * Получение процесса исходя из запроса пользователя
     * 
     * @access protected
     * @return object of \gear\interfaces\IProcess or \Closure
     */
    protected function _routing()
    {
        $process = null;
        $processName = $this->request->get('e');
        if (!$processName)
        {
            $processName = $this->getDefaultProcess();
            if (!$processName)
                $this->e('Unknown process');
        }
        $processes = $this->getProcesses();
        $class = null;
        $properties = array();
        if (isset($processes[$processName]))
        {
            if (is_object($processes[$processName]))
                $process = $processes[$processName];
            else
            if (is_array($processes[$processName]))
            {
                if (isset($processes[$processName]['class']))
                {
                    /** @var array $config */
                    list($class, $config, $properties) = Core::getRecords($this->_processes[$processName]);
                    $properties['name'] = $processName;
                }
                else
                    $properties = array_merge($processes[$processName], array('name' => $processName));
            }
            else
                $properties = array('name' => $processName, 'param' => $processes[$processName]);
        }
        else
            $properties = array('name' => $processName);
        if (!$class)
            $class = $this->_routingClass($processName);
        return $process ? $process : new $class($properties);
    }
    
    /**
     * Получение имя класса процесса
     * 
     * @access protected
     * @param string $processName
     * @return string
     */
    protected function _routingClass($processName)
    {
        $routes = explode('/', $processName);
        $count = count($routes);
        $class = null;
        if ($count == 1)
            $class = Core::app()->getNamespace() . '\process\P' . ucfirst($processName);
        else
        if ($count == 2)
        {
            if ($processName[0] === '/')
                $class = '\\' . $routes[0] . '\process\P' . ucfirst($routes[1]);
            else
                $class = Core::app()->getNamespace() . '\modules\\' . $routes[0] . '\process\P' . ucfirst($routes[1]);
        }
        else
        if ($count >= 3)
            $class = '\\' . $routes[0] . '\modules\\' . $routes[1] . '\process\P' . ucfirst($routes[2]);
        return $class;
    }
    
    /**
     * Установка описания процессов
     * 
     * @access public
     * @param array $processes
     * @return $this
     */
    public function setProcesses(array $processes)
    {
        $this->_processes = $processes;
        return $this;
    }
    
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
     * @return $this
     */
    public function addProcesses(array $processes)
    { 
        $this->_processes = array_merge($this->_processes, $processes);
        return $this; 
    }
    
    /**
     * Добавляет процесс в список
     * 
     * @access public
     * @param string $name
     * @param mixed $process
     * @return $this
     */
    public function addProcess($name, $process)
    {
        $this->_processes[$name] = $process;
        return $this;
    }

    /**
     * Установка текущего процесса
     *
     * @access public
     * @param object|\Closure $process
     * @return $this
     */
    public function setProcess($process)
    {
        if ($process instanceof IProcess || is_callable($process))
            $this->_currentProcess = $process;
        else
            $this->e('Invalid process');
        return $this;
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
     * @return $this
     */
    public function setDefaultProcess($processName)
    {
        $this->_defaultProcess = $processName;
        return $this;
    }
    
    /**
     * Получение названия процесса, исполняемого по-умолчанию
     * 
     * @access public
     * @return string
     */
    public function getDefaultProcess() { return $this->_defaultProcess; }

    public function onProcessNotFound($event, \Exception $e)
    {
        if (Core::app()->isHttp())
        {
            header('HTTP/1.0 404 Not Found', true, 404);
            echo $e->getMessage();
        }
        else
            echo $e->getMessage();
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
