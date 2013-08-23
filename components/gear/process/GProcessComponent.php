<?php

namespace gear\components\gear\process;
use \gear\Core;
use \gear\library\GComponent;
use \gear\library\GException;
use \gear\components\gear\process\GProcess;

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
        'processFolder' => 'process',
        'defaultProcess' => 'index',
        'defaultApi' => 'index',
    );
    protected static $_init = false;
    protected $_processes = array();
    protected $_current = null;
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
     * Исполнение указанного процесса
     * 
     * @access public
     * @param string $process
     * @param string $api
     * @throws ProcessComponentException
     * @return mixed
     */
    public function exec($processName = null, $api = null)
    {
        if (!$processName)
        {
            $processName = $this->i('defaultProcess');
            if (!$processName)
                $this->e('Не указан процесс');
        }
        $namespace = Core::app()->getNamespace();
        if (strpos($processName, '/') !== false)
        {
            list($namespace, $module, $processName) = explode('/', $processName);
            if (!$processName)
                $processName = $module;
            else
                $namespace .= '\\modules\\' . $module;
        }
        $class = $namespace . '\\' . $this->i('processFolder') . '\\G' . ucfirst($processName);
        try
        {
            $properties = isset($this->processes[$processName]) ? $this->processes[$processName] : array();
            $this->_current = new $class($properties);
        }
        catch(GException $e)
        {
            header('HTTP/1.0 404 Not Found', true, 404);
            exit(404);
        }
        $apiName = 'api' . (ucfirst($api ? $api : $this->i('defaultApi')));
        $apiArguments = $this->_prepareArguments($apiName);
        if ($this->_current->event('onBeforeExec'))
        {
            $result = call_user_func_array(array($this->_current, $apiName), $apiArguments);
            return $this->_current->event('onAfterExec', $result);
        }
        return false;
    }
    
    /**
     * Подготовка аргументов, которые могут потребоваться api-методу
     * 
     * @access protected
     * @param string $apiName
     * @return array
     */
    protected function _prepareArguments($apiName)
    {
        $args = $this->getApiArguments($this->_current, $apiName);
        $apiArguments = array();
        foreach($args as $argument)
        {
            $rule = $this->getArgumentRules($this->_current, $argument, $apiName);
            $request = $rule && isset($rule['request']) ? $rule['request'] : 'request';
            $filter = $rule && isset($rule['filter']) ? $rule['filter'] : null;
            try
            {
                $default = $argument->getDefaultValue();
                $apiArguments[] = Core::app()->request->$request($argument->name, $default, $filter);
            }
            catch(\ReflectionException $e)
            {
                if (!($arg = Core::app()->request->$request($argument->name, null, $filter)))
                {
                    $this->e('Api-метод ":apiName" требует обязательного параметра ":argName"', array
                    (
                        ':apiName' => $apiName,
                        ':argName' => $argument->name
                    ));
                }
                $apiArguments[] = $arg;
            }
        }
        return $apiArguments;
    }
    
    /**
     * Возвращает текущий исполняемый процесс
     * 
     * @access public
     * @return \gear\components\gear\process\GProcess
     */
    public function getProcess() { return $this->_current; }
    
    /**
     * Получение правил поступающих от пользователя данных к процессу
     * 
     * @access public
     * @param \gear\components\gear\process\GProcess $process
     * @return array
     */
    public function getRules(GProcess $process) { return $process->getRules(); }

    /**
     * Получение списка параметров указанного метода
     *
     * @access public
     * @param GProcess $process
     * @param string $api
     * @return array
     */
    public function getApiArguments(GProcess $process, $api)
    {
        $reflection = new \ReflectionMethod($process, $api);
        return $reflection->getParameters();
    }
    
    /**
     * Получение правил для указанного аргумента api-метода процесса
     * 
     * @access public
     * @param GProcess $process
     * @param \ReflectionParameter $argument
     * @param null|string $api
     * @return
     */
    public function getArgumentRules(GProcess $process, \ReflectionParameter $argument, $api = null)
    {
        $rules = $this->getRules($process);
        return $api && isset($rules[$api][$argument->name]) 
               ? $rules[$api][$argument->name] 
               : (isset($rules[$argument->name]) ? $rules[$argument->name] : null);
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
