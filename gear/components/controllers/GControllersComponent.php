<?php

namespace gear\components\controllers;

use gear\Core;
use gear\interfaces\IController;
use gear\interfaces\IRequest;
use gear\library\GComponent;
use gear\library\GController;
use gear\library\GEvent;

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
    /**
     * @var string $_defaultControllerName название контроллера исполняемого по-умолчанию
     */
    protected $_defaultControllerName = 'index';
    /**
     * @var IController $_currentController инстанс текущего контроллера
     */
    protected $_currentController = null;
    /**
     * @var array $_mapControllers карта контроллеров
     */
    protected $_mapControllers = [];
    /**
     * @var IRequest $_request пользовательский запрос
     */
    protected $_request = null;
    /* Public */

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

    /**
     * Возвращает экземпляр текущего контроллера
     *
     * @return IController
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrentController(): IController
    {
        if (!($this->_currentController instanceof IController)) {
            $path = trim((string)$this->request->r);
            $this->_currentController = $this->getRouteController($path);
        }
        return $this->_currentController;
    }

    /**
     * Возвращает название контроллера, исполняемого по-умолчанию
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultControllerName(): string
    {
        return $this->_defaultControllerName;
    }

    /**
     * Возвращает карту путей к контроллерам
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMapControllers(): array
    {
        return $this->_mapControllers;
    }

    /**
     * Возвращает экземпляр запроса
     *
     * @return IRequest
     * @since 0.0.1
     * @version 0.0.1
     */
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
     * Возвращает контроллер согласно пути из запроса
     *
     * @param string $path
     * @return IController
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRouteController(string $path): IController
    {
        list($controllerPath,) = explode('/', $path);
        if ($controllerPath) {
            $elems = explode('_', $controllerPath);
            $c = count($elems);
        } else {
            $c = 0;
            $elems = [];
        }
        $name = $c > 0 ? array_pop($elems) : $this->defaultControllerName;
        $class = ucfirst($name) . 'Controller';
        $properties = ['name' => $name];
        if ($c === 0 || $c === 1) {
            $map = $this->existsInMapControllers($name, true);
            if ($map instanceof \Closure) {
                $properties = ['name' => $name, 'bindingClosure' => $map];
                $controller = 'GController';
            } else {
                if ($map) {
                    $controller = $map;
                } else {
                    $controller = Core::app()->namespace . '\controllers\\' . $class;
                }
            }
        } else if ($c > 1) {
            $p = implode('\\', $elems);
            if ($controllerPath{0} === '_') {
                $controller = '\\' . $p . '\controllers\\' . $class;
            } else {
                $namespace = Core::app()->namespace;
                $controller = $namespace . '\\' . $p . '\controllers\\' . $class;
            }
        }
        $controller = new $controller($properties, $this);
        return $controller;
    }

    /**
     * Возвращает true, если контроллер присутствует в карте, иначе false
     *
     * @param string $name
     * @param bool $returnPath
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function existsInMapControllers(string $name, bool $returnPath = false)
    {
        if ($returnPath) {
            return isset($this->_mapControllers[$name]) ? $this->_mapControllers[$name] : false;
        } else {
            return in_array($name, $this->_mapControllers, true);
        }
    }

    /**
     * Установка текущего контроллера
     *
     * @param \Closure|array|IController $controller
     * @since 0.0.1
     * @version 0.0.1
     */
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
            $this->exceptionController('Invalid controller');
        }
    }

    /**
     * Устанавливает название контроллера, исполняемого по-умолчанию
     *
     * @param string $defaultControllerName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDefaultControllerName(string $defaultControllerName)
    {
        $this->_defaultControllerName = $defaultControllerName;
    }

    /**
     * Устанавливает карту путей к контроллерам
     *
     * @param array $mapControllers
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setMapControllers(array $mapControllers)
    {
        $this->_mapControllers = $mapControllers;
    }

    public function setRequest(IRequest $request)
    {
        $this->_request = $request;
    }

    public function beforeExecRouting($request)
    {
        return Core::trigger('onBeforeExecRouting', new GEvent($this, ['target' => $this, 'request' => $request]));
    }
}
