<?php

namespace gear\library;

use gear\Core;
use gear\library\GModule;
use gear\library\GEvent;

/** 
 * Класс описывающий приложение
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 * @php 5.3.x
 * @release 1.0.0
 */
class GApplication extends GModule
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config =
    [
        'components' =>
        [
            'process' =>
            [
                'class' => '\gear\components\gear\process\GProcessComponent',
                'defaultProcess' => 'index',
            ],
        ],
        'plugins' =>
        [
            /* Плагин для работы с http-запросами */
            'request' => ['class' => '\gear\plugins\gear\http\GRequest'],
            /* Плагин для работы с окружением */
            'env' => ['class' => '\gear\plugins\gear\http\GEnvironment'],
            /* Плагин для работы с http */
            'http' => ['class' => '\gear\plugins\gear\http\GHttp'],
            /* Плагин для протоколирования работы  */
            'log' => ['class' => '\gear\plugins\gear\GLog'],
        ],
    ];
    protected static $_init = false;
    protected $_namespace = null;
    /* Список callback-функция для работы с выводимыми данными */
    protected $_outputCallbacks = [];
    /* Буфер вывода */
    protected $_outputData = null;
    /* Public */
    
    /**
     * Запуск приложения
     * 
     * @access public
     * @return void
     */
    public function run($process = null, $request = null)
    {
        if (Core::trigger('onBeforeApplicationRun', new GEvent($this, ['process' => $process, 'request' => $request])))
        {
            if ($request)
                $this->request->setData($request);
            $result = $this->c('process')->exec($process, $this->request);
            /** @var mixed $result */
            Core::trigger('onAfterApplicationRun', new GEvent($this), $result);
        }
    }

    /**
     * Принудительная смена пространства имён приложения
     *
     * @access public
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * Возвращает либо Core::HTTP, если приложение запущено из браузера, либо
     * Core::CLI, если приложение запущено из консоли.
     * 
     * @access public
     * @see Core::HTTP
     * @see Core::CLI
     * @see Core::getMode()
     * @return integer
     */
    public function getMode() { return Core::getMode(); }
    
    /**
     * Возвращает true, если приложение запущено из браузера
     * 
     * @access public
     * @return bool
     */
    public function isHttp() { return $this->getMode() === Core::HTTP; }
    
    /**
     * Возвращает true, если приложение запущено из консоли
     * 
     * @access public
     * @return bool
     */
    public function isCli() { return $this->getMode() === Core::CLI; }

    /**
     * Возвращает текущий исполняемый процесс
     * 
     * @access public
     * @return object
     */
    public function getProcess() { return $this->c('process')->getProcess(); }

    /**
     * Вывод данных
     *
     * @access public
     * @param string $data
     * @param bool $buffering
     * @return $this
     */
    public function out($data, $buffering = false)
    {
        foreach($this->outputCallbacks as $callback)
        {
            if (is_callable($callback))
                $data = $callback($data);
        }
        if ($buffering)
            $this->_outputData .= $data;
        else
            echo $data;
        return $this;
    }

    /**
     * Возвращает массив callback-функций, работающих с выводимыми Данными
     *
     * @access public
     * @return array
     */
    public function getOutputCallbacks() { return $this->_outputCallbacks; }

    /**
     * Установка списка callback-функций, работающих с выводимыми Данными
     *
     * @access public
     * @return $this
     */
    public function setOutputCallbacks(array $callbacks)
    {
        $this->_outputCallbacks = $callbacks;
        return $this;
    }

    /**
     * Добавление callback-функции, работающего с выводимыми Данными
     *
     * @access public
     * @return $this
     */
    public function addOutputCallbacks(array $callbacks)
    {
        $this->_outputCallbacks = $callbacks;
        return $this;
    }

    /**
     * Обработчик события, вызываемого на этапе создания объекта (из
     * конструктора)
     * 
     * @access public
     * @param GEvent $event
     * @return boolean
     */
    public function onConstructed()
    {
        parent::onConstructed();
        Core::attachEvent('onBeforeApplicationRun', [$this, 'onBeforeRun']);
        Core::attachEvent('onAfterApplicationRun', [$this, 'onAfterRun']);
        return true;
    }

    public function onBeforeRun($event) { return true; }
    
    public function onAfterRun($event) { return true; }
}
