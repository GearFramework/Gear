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
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GProcess extends GModel implements IProcess
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_access = Core::ACCESS_PUBLIC;
    protected $_apis = [];
    protected $_currentApi = null;
    protected $_request = [];
    protected $_rules = [];
    protected $_defaultApi = 'index';
    /* Public */
    public $name = '';
    
    /**
     * Неявный вызов entry()
     * 
     * @access public
     * @return mixed
     */
    public function __invoke() { return call_user_func_array([$this, 'entry'], func_get_args()); }

    public function setDefaultApi($apiName)
    {
        $this->_defaultApi = $apiName;
        return $this;
    }

    public function getDefaultApi() { return $this->_defaultApi; }
    
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
            Core::syslog('PROCESS -> ' . get_class($this) . ' default api ' . $this->defaultApi . ' [' . __LINE__ . ']');
            $apiName = $this->request->get('f', $this->defaultApi, function($value) { return preg_replace('/[^a-zA-Z0-9_]/', '', $value); });
            Core::syslog('PROCESS -> ' . get_class($this) . ' prepare api ' . $apiName . ' [' . __LINE__ . ']');
            $api = $this->getApis($apiName);
            if ($api)
            {
                if ($api instanceof \Closure)
                    $this->_currentApi = $api;
                else
                {
                    list($class, $config, $properties) = Core::getRecords($api);
                    $this->_currentApi = [new $class($properties, $this), 'entry'];
                }
            }
            else
            {
                $api = 'api' . ucfirst($apiName);
                if (!method_exists($this, $api))
                    throw $this->exceptionProcessApiNotExists(['apiName' => $apiName, 'processName' => $this->name]);
                $this->_currentApi = [$this, $api];
            }
            $arguments = $this->_prepareArguments($apiName);
            Core::syslog('PROCESS -> ' . get_class($this) . ' run api ' . $apiName . ' [' . __LINE__ . ']');
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
        $apiArguments = [];
        $request = $this->request->request();
        foreach($args as $argument)
        {
            $rule = $this->getArgumentRules($apiName, $argument);
            $value = isset($request[$argument->name]) ? $request[$argument->name] : null;
            if ($value === null)
            {
                if (!$argument->isOptional())
                    throw $this->exceptionApiInvalidRequestParameter(['apiName' => $apiName, 'argName' => $argument->name]);
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
    public function setAccess($value) { throw $this->exceptionObjectPropertyIsReadOnly(['preopertyName' => 'access']); }

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
        $process = $this;
        Core::on('onBeforeProcessExecute', function($event, $request) use($process) { return $process->onBeforeExec($event, $request); });
        Core::on('onAfterProcessExecute', function($event, $result = true) use($process) { return $process->onAfterExec($event, $result); });
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
    public function beforeExec($event, $request) { return Core::trigger('onBeforeProcessExecute', $event, $request); }
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
    public function afterExec($event, $result = true) { return Core::trigger('onAfterProcessExecute', $event, $result); }
    public function onAfterExec($event, $result = true) { return true; }
}
