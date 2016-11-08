<?php

namespace gear\components\controllers;

use gear\Core;
use gear\interfaces\IController;
use gear\interfaces\IRequest;
use gear\library\GComponent;
use gear\library\GController;

/**
 * Роутинг контроллеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GControllersComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_controllers = [];
    /**
     * @var IController $_currentController инстанс текущего контроллера
     */
    protected $_currentController = null;
    protected $_mapControllers = [];
    protected $_request = null;
    /* Public */

    public function getCurrentController(): IController
    {
        if (!($this->_currentController instanceof IController)) {

        }
        return $this->_currentController;
    }

    public function getRequest(): IRequest
    {
        if ($this->_request === null) {
            if (is_object($this->owner)) {
                $this->_request = $this->owner->request();
            } else {
                $this->_request = Core::app()->request();
            }
        }
        return $this->_request;
    }

    /**
     * Получение и запуск нужного контроллера
     *
     * @param IRequest $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exec(IRequest $request)
    {
        $result = false;
        if ($this->beforeExecRouting($request)) {
            $this->request = $request;
            $controller = $this->currentController;
            /** @var \Closure|\gear\interfaces\IController $controller */
            $controller($request);
        }
        return $result;
    }

    public function setCurrentController($controller)
    {
        if ($controller instanceof \Closure) {
            $controller = new GController(['bindingClosure' => $controller], $this);
        } else if (is_array($controller)) {
            list($class,, $properties) = Core::configure($controller);
            if ($class) {
                $controller = new $class($properties, $this);
            }
        }
        if ($controller instanceof IController) {
            $this->_currentController = $controller;
        } else {
            $this->exceptionController('Invalid current controller');
        }
    }

    public function setRequest(IRequest $request)
    {
        $this->_request = $request;
    }
}
