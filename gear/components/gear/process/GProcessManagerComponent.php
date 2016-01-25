<?php

namespace gear\components\gear\process;

use gear\Core;
use gear\interfaces\IProcess;
use gear\library\GComponent;

/**
 * Компонент обслуживающий процессы
 *
 * @package Gear Framework
 * @component ProcessComponent
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GProcessManagerComponent extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    /* Настройки компонента */
    protected static $_config = [];
    protected static $_init = false;
    /* Объект возвращающий параметры запроса */
    protected $_request = null;
    /* Имя процесса исполняемого по-умолчанию */
    protected $_defaultProcess = 'index';
    /* Префикс классов-процессов */
    protected $_prefixClassProcess = 'P';
    /* Список установленных процессов */
    protected $_processes = [];
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
        try {
            $this->request = !$request || !is_object($request) ? Core::app()->request : $request;
            if ($process && ($process instanceof IProcess || $process instanceof \Closure))
                $this->_currentProcess = $process;
            else
                $this->_currentProcess = $this->_routing();
        } catch (GException $e) {
            $this->trigger('onProcessNotFound', $e);
            return false;
        }
        $result = false;
        if (Core::trigger('onBeforeExec', $this->_currentProcess, $request)) {
            if ($this->_currentProcess instanceof \Closure) {
                Core::syslog(__CLASS__ . ' -> Execute closure process [' . __LINE__ . ']');
                $result = call_user_func($this->_currentProcess, $this->request);
                Core::trigger('onAfterExec', $this->_currentProcess, $result);
            } else {
                Core::syslog(__CLASS__ . ' -> Execute base process ' . get_class($this->_currentProcess) . ' [' . __LINE__ . ']');
                $result = $this->_currentProcess->entry($this->request);
            }
        }
        return $result;
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
        $processName = $this->request->get('e', $this->defaultProcess);
        Core::syslog(__CLASS__ . ' -> Prepare request process ' . $processName . ' [' . __LINE__ . ']');
        if (!$processName)
            throw $this->exceptionProcess('Unknown process');
        //$processes = $this->getProcesses();
        $class = null;
        $properties = [];
        if (isset($this->processes[$processName])) {
            $process = $this->processes[$processName];
            if (is_array($process)) {
                if (isset($process['class'])) {
                    /** @var array $config */
                    list($class, , $properties) = Core::getRecords($process);
                    $properties['name'] = $processName;
                } else
                    $properties = array_merge($process, ['name' => $processName]);
            } else
                $properties = ['name' => $processName, 'param' => $process];
        } else
            $properties = ['name' => $processName];
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
        if ($count == 1) {
            $class = Core::app()->getNamespace() . '\process\P' . ucfirst($processName);
            Core::syslog(__CLASS__ . ' -> Class routed ' . $class . ' [' . __LINE__ . ']');
        }
        else
            if ($count == 2) {
                if ($processName[0] === '/')
                    $class = '\\' . $routes[0] . '\process\P' . ucfirst($routes[1]);
                else
                    $class = Core::app()->getNamespace() . '\modules\\' . $routes[0] . '\process\P' . ucfirst($routes[1]);
            } else
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
    public function getProcesses()
    {
        return $this->_processes;
    }

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
            throw $this->exceptionProcess('Invalid process');
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

    /**
     * Установка прфекса для классов-процессов
     *
     * @access public
     * @param string $prefix
     * @return $this
     */
    public function setPrefixClassProcess($prefix)
    {
        $this->_prefixClassProcess = $prefix;
        return $this;
    }

    /**
     * Получение прфекса для классов-процессов
     *
     * @access public
     * @return string
     */
    public function getPrefixClassProcess() { return $this->_prefixClassProcess; }

    public function onProcessNotFound($event, \Exception $e) { throw $this->exceptionHttpError($e->getMessage(), ['request' => $this->request], 404); }
}
