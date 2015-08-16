<?php

namespace gear\models;
use gear\Core;
use gear\library\GModel;
use gear\library\GException;
use gear\library\GEvent;
use gear\interfaces\IProcess;

/** 
 * Класс процессов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.3.x
 * @release 1.0.0
 */
class GProcess extends GModel implements IProcess
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_access = Core::ACCESS_PUBLIC;
    protected $_apis = array();
    protected $_currentApi = null;
    protected $_request = array();
    protected $_rules = array();
    /* Public */
    public $defaultApi = 'index';
    public $name = '';
    
    /**
     * Неявный вызов entry()
     * 
     * @access public
     * @return mixed
     */
    public function __invoke() { return call_user_func_array(array($this, 'entry'), func_get_args()); }
    
    /**
     * Точка входа в процесс
     * 
     * @access public
     * @param object $request
     * @return mixed
     */
    public function entry($request = null)
    {
        if ($request && !is_object($request))
            $request = Core::app()->request;
        $this->request = $request;
        if ($this->beforeExec(new GEvent($this), $this->request))
        {
            $apiName = $this->request->get('f', $this->defaultApi, function($value) { return preg_replace('/\W/', '', $value); });
            $api = $this->getApis($apiName);
            if ($api)
            {
                if ($api instanceof \Closure)
                    $this->_currentApi = $api;
                else
                {
                    list($class, $config, $properties) = Core::getRecords($api);
                    $this->_currentApi = array(new $class($properties, $this), 'runApi');
                }
            }
            else
            {
                $api = 'api' . ucfirst($apiName);
                if (!method_exists($this, $api))
                    throw $this->exceptionProcessApiNotExists(array('apiName' => $apiName, 'processName' => $this->name));
                $this->_currentApi = array($this, $api);
            }
            $arguments = $this->_prepareArguments($apiName);
            $result = call_user_func_array($this->_currentApi, $arguments);
            $this->afterExec(new GEvent($this), $result);
            return $result;
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
        $args = $this->getApiArguments($this->_currentApi);
        $apiArguments = array();
        $request = $this->request->request();
        foreach($args as $argument)
        {
            $rule = $this->getArgumentRules($apiName, $argument);
            $value = isset($request[$argument->name]) ? $request[$argument->name] : null;
            if ($value === null)
            {
                if (!$argument->isOptional())
                    throw $this->exceptionApiInvalidRequestParameter(array('apiName' => $apiName, 'argName' => $argument->name));
                $value = $argument->getDefaultValue();
            }
            else
            if (isset($rule['filter']))
                $value = $this->request->filtering($rule['filter'], $value);
            $apiArguments[] = $value;
        }
        return $apiArguments;
    }
    
    /**
     * Получение списка параметров указанного метода
     *
     * @access public
     * @param string $api
     * @return array
     */
    public function getApiArguments($api)
    {
        if ($api instanceof \Closure)
            $reflection = new \ReflectionFunction($api);
        else
        {
            list($instance, $apiName) = $api;
            $reflection = new \ReflectionMethod($instance, $apiName);
        }
        return $reflection->getParameters();
    }
    
    /**
     * Получение правил для указанного аргумента api-метода процесса
     * 
     * @access public
     * @param string $apiName
     * @param \ReflectionParameter $argument
     * @return
     */
    public function getArgumentRules($apiName, \ReflectionParameter $argument)
    {
        return isset($this->rules[$apiName][$argument->name]) ? $this->rules[$apiName][$argument->name] : null;
    }

    /**
     * Установка описания для внешних функций api процесса
     *
     * @access public
     * @param array $apis as api list
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
     * @param object $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Получение параметров запроса
     *
     * @access public
     * @return object
     */
    public function getRequest() { return $this->_request; }

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
     * @param integer $value only one: Core::ACCESS_PRIVATE|Core::ACCESS_PROTECTED|Core::ACCESS_PUBLIC
     * @return void
     */
    public function setAccess($value) { throw $this->exceptionObjectPropertyIsReadOnly(array('preopertyName' => 'access')); }

    /**
     * Установка правил обработки поступающих данных от пользоваля к
     * api-методам процесса
     *
     * @access public
     * @param array $rules
     * @return array
     */
    public function setRules(array $rules) { return $this->_rules = $rules; }

    /**
     * Возвращает массив правил обработки поступающих данных от пользоваля к
     * api-методам процесса
     * 
     * @access public
     * @return array
     */
    public function getRules() { return $this->_rules; }

    /**
     * @access public
     * @return bool
     * @see GObject::onConstructed()
     */
    public function onConstructed()
    {
        parent::onConstructed();
        Core::attachEvent('onBeforeProcessExecute', [$this, 'onBeforeExec']);
        Core::attachEvent('onAfterProcessExecute', [$this, 'onAfterExec']);
        return true;
    }

    /**
     * Обработчик по-умолчанию события возникающего перед исполнением
     * процесса
     * 
     * @access public
     * @param GEvent $event
     * @return bool
     */
    public function beforeExec($event, $request) { return Core::on('onBeforeProcessExecute', $event, $request); }
    public function onBeforeExec($event, $request = null) { return true; }
    
    /**
     * Обработчик по-умолчанию события возникающего после исполнения
     * процесса
     * 
     * @access public
     * @param GEvent $event
     * @param mixed $result
     * @return mixed
     */
    public function afterExec($event, $result = true) { return Core::on('onAfterProcessExecute', $event, $result); }
    public function onAfterExec($event, $result = true) { return true; }
}
