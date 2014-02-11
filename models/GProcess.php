<?php

namespace gear\models;
use gear\Core;
use gear\library\GModel;
use gear\library\GException;
use gear\library\GEvent;

/** 
 * Класс процессов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GProcess extends GModel implements \gear\interfaces\IProcess
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_access = Core::ACCESS_PUBLIC;
    protected $_apis = array();
    protected $_currentApi = null;
    protected $_request = array();
    /* Public */
    public $defaultApi = 'index';
    public $rules = array();
    public $name = '';
    
    /**
     * Неявный вызов entry()
     * 
     * @access public
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array(array($this, 'entry'), func_get_args());
    }
    
    /**
     * Точка входа в процесс
     * 
     * @access public
     * @param array $request
     * @return mixed
     */
    public function entry($request = array())
    {
        if ($this->event('onBeforeExec', new GEvent($this), $request))
        {
            $this->request = $request;
            $apiName = isset($request['f']) ? $request['f'] : $this->defaultApi;
            $api = $this->getApis($apiName);
            if ($api)
            {
                if ($api instanceof \Closure)
                    $this->_currentApi = $api;
                else
                {
                    list($class, $config, $properties) = Core::getRecords($api);
                    $properties['owner'] = $this;
                    $this->_currentApi = array(new $class($properties), 'runApi');
                }
            }
            else
            {
                $api = 'api' . ucfirst($apiName);
                if (!method_exists($this, $api))
                    $this->e('Api ":apiName" is not exists in process ":processName"', array('apiName' => $apiName, 'processName' => $this->name));
                $this->_currentApi = array($this, $api);
            }
            $arguments = $this->_prepareArguments($apiName, $request);
            $result = call_user_func_array($this->_currentApi, $arguments);
            $this->event('onAfterExec', new GEvent($this), $result);
            return $result;
        }
        return false;
    }

    /**
     * Установка описания для внешних функций api процесса
     * 
     * @access public
     * @param array as api list $apis
     * @return void
     */
    public function setApis(array $apis) { $this->_apis = $apis; }
    
    /**
     * Получение списка внешних api-функций или одной указанной
     * 
     * @access public
     * @param string $name
     * @return mixed
     */
    public function getApis($name = null)
    {
        return !$name ? $this->_apis : (isset($this->_apis[$name]) ? $this->_apis[$name] : null);
    }
    
    /**
     * Установка параметров запроса GET|POST
     * 
     * @access public
     * @param array $request
     * @return void
     */
    public function setRequest(array $request)
    {
        $this->_request = $request;
    }
    
    /**
     * Получение параметров запроса
     * 
     * @access public
     * @return array
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Подготовка аргументов, которые могут потребоваться api-методу
     * 
     * @access protected
     * @param string $apiName
     * @param array $request
     * @return array
     */
    protected function _prepareArguments($apiName, $request)
    {
        
        $args = $this->getApiArguments($this->_currentApi);
        $apiArguments = array();
        foreach($args as $argument)
        {
            $rule = $this->getArgumentRules($apiName, $argument);
            $value = isset($request[$argument->name]) ? $request[$argument->name] : null;
            if ($value === null)
            {
                if (!$argument->isOptional())
                    $this->e('Api ":apiName" required parameter ":argName"', array
                    (
                        ':apiName' => $apiName,
                        ':argName' => $argument->name
                    ));
                $value = $argument->getDefaultValue();
            }
            else
            if (isset($rule['filter']))
                $value = Core::app()->request->filtering($rule['filter'], $value);
            $apiArguments[] = $value;
        }
        return $apiArguments;
    }
    
    /**
     * Получение списка параметров указанного метода
     *
     * @access public
     * @param GProcess $process
     * @param string $api
     * @return array
     */
    public function getApiArguments($api)
    {
        if (is_array($api))
        {
            list($instance, $apiName) = $api;
            $reflection = new \ReflectionMethod($instance, $apiName);
        }
        else
        if ($api instanceof \Closure)
            $reflection = new ReflectionFunction($api);
        return $reflection->getParameters();
    }
    
    /**
     * Получение правил для указанного аргумента api-метода процесса
     * 
     * @access public
     * @param string $api
     * @param \ReflectionParameter $argument
     * @return
     */
    public function getArgumentRules($apiName, \ReflectionParameter $argument)
    {
        return isset($this->rules[$apiName][$argument->name]) ? $this->rules[$apiName][$argument->name] : null;
    }

    /**
     * Получение уровня доступа к процессу
     * 
     * @access public
     * @return Core::ACCESS_PRIVATE|Core::ACCESS_PROTECTED|Core::ACCESS_PUBLIC
     */
    public function getAccess() { return $this->_access; }
    
    /**
     * Запрет на установку уроня доступа извне. Уровень доступа возможно
     * пописать либо непосредстве при реализации нового класса процесса,
     * либо в описании компонента, управляющего процессами
     * 
     * @access public
     * @param integer one from Core::ACCESS_PRIVATE|Core::ACCESS_PROTECTED|Core::ACCESS_PUBLIC $value
     * @return void
     */
    public function setAccess($value) { $this->e('"access" is read-only property'); }
    
    /**
     * Возвращает массив правил обработки поступающих данных от пользоваля к
     * api-методам процесса
     * 
     * @access public
     * @return array
     */
    public function getRules() { return $this->rules; }
    
    /**
     * Обработчик по-умолчанию события возникающего перед исполнением
     * процесса
     * 
     * @access public
     * @param GEvent $event
     * @return bool
     */
    public function onBeforeExec($event) { return true; }
    
    /**
     * Обработчик по-умолчанию события возникающего после исполнения
     * процесса
     * 
     * @access public
     * @param GEvent $event
     * @param mixed $result
     * @return mixed
     */
    public function onAfterExec($event, $result = true) { return $result; }
}

/** 
 * Класс исключений процесса
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class ProcessException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
